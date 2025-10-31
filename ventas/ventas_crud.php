<?php
require_once '../conexion.php';
$db = conectar();

// --------------------------------------------------
// AGREGAR VENTA (con validación y descuento en inventario)
// --------------------------------------------------
if (isset($_POST['agregar'])) {
    $fecha = $_POST['fecha_venta'];
    $total = $_POST['total_venta'];
    $id_mesa = empty($_POST['id_mesa']) ? 'NULL' : "'".$_POST['id_mesa']."'";
    $id_usuario = $_POST['id_usuario'];
    $id_sucursal = $_POST['id_sucursal'];

    // Empezar transacción
    $db->begin_transaction();

    try {
        // 1) Determinar el id_almacen asociado a la sucursal (usamos el primero si hay varios)
        $id_almacen = null;
        $qAlmacen = "SELECT id_almacen FROM almacenes_sucursal WHERE id_sucursal = $id_sucursal LIMIT 1";
        $rAlmacen = $db->query($qAlmacen);
        if ($rAlmacen && $rAlmacen->num_rows > 0) {
            $rowAl = $rAlmacen->fetch_assoc();
            $id_almacen = $rowAl['id_almacen'];
        } else {
            // Si no hay almacén para la sucursal, abortamos
            throw new Exception("No se encontró almacén para la sucursal ID $id_sucursal.");
        }

        // 2) Antes de insertar la venta, verificamos stock para *todos* los platos/ingredientes
        // Recopilamos los requisitos totales por insumo (sumar si el mismo insumo aparece en varios platos)
        $requeridos = []; // id_insumo => cantidad_total_necesaria

        foreach ($_POST['id_plato'] as $i => $id_plato) {
            $cantidadPlatos = floatval($_POST['cantidad'][$i]);
            if (empty($id_plato) || $cantidadPlatos <= 0) continue;

            // Obtener id_receta(s) asociadas al plato
            $qRecetas = "SELECT id_receta FROM recetas WHERE id_plato = $id_plato";
            $rRecetas = $db->query($qRecetas);
            if ($rRecetas === false) throw new Exception("Error al obtener recetas para el plato ID $id_plato.");

            while ($rec = $rRecetas->fetch_assoc()) {
                $id_receta = $rec['id_receta'];

                // Obtener ingredientes de detalle_receta
                $qDet = "SELECT id_insumo, cantidad FROM detalle_receta WHERE id_receta = $id_receta";
                $rDet = $db->query($qDet);
                if ($rDet === false) throw new Exception("Error al obtener detalle_receta para receta $id_receta.");

                while ($ing = $rDet->fetch_assoc()) {
                    $id_insumo = $ing['id_insumo'];          // refiere a materiaprima.id_materia_prima
                    $cant_por_plato = floatval($ing['cantidad']);
                    $necesario = $cant_por_plato * $cantidadPlatos;

                    if (!isset($requeridos[$id_insumo])) $requeridos[$id_insumo] = 0;
                    $requeridos[$id_insumo] += $necesario;
                }
            }
        }

        // 3) Validar stock para cada insumo en inventario_materiaprima (filtrado por id_almacen)
        foreach ($requeridos as $id_insumo => $cantidad_necesaria) {
            // Obtener stock actual (por id_insumo e id_almacen)
            $qStock = "SELECT im.stock, mp.nombre_insumos
                       FROM inventario_materiaprima im
                       LEFT JOIN materiaprima mp ON im.id_insumo = mp.id_materia_prima
                       WHERE im.id_insumo = $id_insumo AND im.id_almacen = $id_almacen
                       LIMIT 1";
            $rStock = $db->query($qStock);
            if ($rStock === false) throw new Exception("Error al consultar stock del insumo ID $id_insumo.");
            if ($rStock->num_rows == 0) {
                // No hay registro de inventario para ese insumo en ese almacén
                $qName = $db->query("SELECT nombre_insumos FROM materiaprima WHERE id_materia_prima = $id_insumo LIMIT 1");
                $name = ($qName && $qName->num_rows) ? $qName->fetch_assoc()['nombre_insumos'] : "ID $id_insumo";
                throw new Exception("No existe inventario del insumo '$name' (ID $id_insumo) en el almacén de la sucursal.");
            }
            $rowStock = $rStock->fetch_assoc();
            $stockActual = floatval($rowStock['stock']);
            $nombreInsumo = $rowStock['nombre_insumos'] ?? ("ID $id_insumo");

            if ($stockActual < $cantidad_necesaria) {
                throw new Exception("Stock insuficiente para '$nombreInsumo'. Disponible: $stockActual, requerido: $cantidad_necesaria.");
            }
        }

        // 4) Si pasamos validación, insertamos la venta
        $sqlVenta = "INSERT INTO ventas (fecha_venta, total_venta, id_mesa, id_usuario, id_sucursal)
                     VALUES ('$fecha', '$total', $id_mesa, '$id_usuario', '$id_sucursal')";
        if (!$db->query($sqlVenta)) throw new Exception("Error al insertar venta: " . $db->error);
        $id_venta = $db->insert_id;

        // 5) Insertar detalle_venta y descontar inventario (ya validado)
        foreach ($_POST['id_plato'] as $i => $id_plato) {
            $cantidadPlatos = floatval($_POST['cantidad'][$i]);
            $precio = floatval($_POST['precio_unitario'][$i]);

            if (empty($id_plato) || $cantidadPlatos <= 0) continue;

            // Insertar detalle_venta
            $sqlDetalle = "INSERT INTO detalle_venta (id_venta, id_plato, cantidad, precio_unitario)
                           VALUES ($id_venta, $id_plato, $cantidadPlatos, $precio)";
            if (!$db->query($sqlDetalle)) throw new Exception("Error al insertar detalle_venta: " . $db->error);

            // Obtener recetas del plato e ir descontando ingrediente por ingrediente
            $qRecetas = "SELECT id_receta FROM recetas WHERE id_plato = $id_plato";
            $rRecetas = $db->query($qRecetas);
            while ($rec = $rRecetas->fetch_assoc()) {
                $id_receta = $rec['id_receta'];
                $qDet = "SELECT id_insumo, cantidad FROM detalle_receta WHERE id_receta = $id_receta";
                $rDet = $db->query($qDet);

                while ($ing = $rDet->fetch_assoc()) {
                    $id_insumo = $ing['id_insumo'];
                    $cant_por_plato = floatval($ing['cantidad']);
                    $cantidadUsada = $cant_por_plato * $cantidadPlatos;

                    // Descontar del inventario del almacén correspondiente
                    $qUpd = "UPDATE inventario_materiaprima
                             SET stock = stock - $cantidadUsada
                             WHERE id_insumo = $id_insumo AND id_almacen = $id_almacen";
                    if (!$db->query($qUpd)) throw new Exception("Error al actualizar inventario: " . $db->error);
                }
            }
        }

        // 6) Commit si todo bien
        $db->commit();
        header("Location: ventas.php?agregado=1");
        exit;

    } catch (Exception $e) {
        // Revertir y mostrar error
        $db->rollback();
        // Puedes adaptar el manejo de error (por ejemplo, redirigir con mensaje en GET)
        die("Error al procesar la venta: " . $e->getMessage());
    }
}

// --------------------------------------------------
// EDITAR VENTA (solo datos generales)
// --------------------------------------------------
if (isset($_POST['editar'])) {
    $id = $_POST['id_venta'];
    $fecha = $_POST['fecha_venta'];
    $total = $_POST['total_venta'];
    $id_mesa = empty($_POST['id_mesa']) ? 'NULL' : "'".$_POST['id_mesa']."'";
    $id_usuario = $_POST['id_usuario'];
    $id_sucursal = $_POST['id_sucursal'];

    $sql = "UPDATE ventas 
            SET fecha_venta='$fecha',
                total_venta='$total',
                id_mesa=$id_mesa,
                id_usuario='$id_usuario',
                id_sucursal='$id_sucursal'
            WHERE id_venta=$id";
    $db->query($sql);

    header("Location: ventas.php?editado=1");
    exit;
}

// --------------------------------------------------
// ELIMINAR VENTA (no repone inventario; se puede mejorar si quieres reponer)
// --------------------------------------------------
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    // Primero eliminar los detalles asociados
    $db->query("DELETE FROM detalle_venta WHERE id_venta=$id");

    // Luego eliminar la venta principal
    $db->query("DELETE FROM ventas WHERE id_venta=$id");

    header("Location: ventas.php?eliminado=1");
    exit;
}
?>
