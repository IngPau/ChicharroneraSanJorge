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

    
    $id_cliente = isset($_POST['id_cliente']) && $_POST['id_cliente'] !== '' 
        ? intval($_POST['id_cliente']) 
        : 'NULL';

   
    $db->begin_transaction();

    try {
        
        $id_almacen = null;
        $qAlmacen = "SELECT id_almacen FROM almacenes_sucursal WHERE id_sucursal = $id_sucursal LIMIT 1";
        $rAlmacen = $db->query($qAlmacen);
        if ($rAlmacen && $rAlmacen->num_rows > 0) {
            $rowAl = $rAlmacen->fetch_assoc();
            $id_almacen = $rowAl['id_almacen'];
        } else {
            throw new Exception("No se encontró almacén para la sucursal ID $id_sucursal.");
        }

        // 2) Verificar stock requerido
        $requeridos = []; // id_insumo => cantidad_total_necesaria
        foreach ($_POST['id_plato'] as $i => $id_plato) {
            $cantidadPlatos = floatval($_POST['cantidad'][$i]);
            if (empty($id_plato) || $cantidadPlatos <= 0) continue;

            $qRecetas = "SELECT id_receta FROM recetas WHERE id_plato = $id_plato";
            $rRecetas = $db->query($qRecetas);
            if ($rRecetas === false) throw new Exception("Error al obtener recetas para el plato ID $id_plato.");

            while ($rec = $rRecetas->fetch_assoc()) {
                $id_receta = $rec['id_receta'];
                $qDet = "SELECT id_insumo, cantidad FROM detalle_receta WHERE id_receta = $id_receta";
                $rDet = $db->query($qDet);
                if ($rDet === false) throw new Exception("Error al obtener detalle_receta para receta $id_receta.");

                while ($ing = $rDet->fetch_assoc()) {
                    $id_insumo = $ing['id_insumo'];
                    $cant_por_plato = floatval($ing['cantidad']);
                    $necesario = $cant_por_plato * $cantidadPlatos;

                    if (!isset($requeridos[$id_insumo])) $requeridos[$id_insumo] = 0;
                    $requeridos[$id_insumo] += $necesario;
                }
            }
        }

        // 3) Validar stock disponible
        foreach ($requeridos as $id_insumo => $cantidad_necesaria) {
            $qStock = "SELECT im.stock, mp.nombre_insumos
                       FROM inventario_materiaprima im
                       LEFT JOIN materiaprima mp ON im.id_insumo = mp.id_materia_prima
                       WHERE im.id_insumo = $id_insumo AND im.id_almacen = $id_almacen
                       LIMIT 1";
            $rStock = $db->query($qStock);
            if ($rStock === false) throw new Exception("Error al consultar stock del insumo ID $id_insumo.");
            if ($rStock->num_rows == 0) {
                $qName = $db->query("SELECT nombre_insumos FROM materiaprima WHERE id_materia_prima = $id_insumo LIMIT 1");
                $name = ($qName && $qName->num_rows) ? $qName->fetch_assoc()['nombre_insumos'] : "ID $id_insumo";
                throw new Exception("No existe inventario del insumo '$name' en el almacén de la sucursal.");
            }

            $rowStock = $rStock->fetch_assoc();
            $stockActual = floatval($rowStock['stock']);
            $nombreInsumo = $rowStock['nombre_insumos'] ?? ("ID $id_insumo");

            if ($stockActual < $cantidad_necesaria) {
                throw new Exception("Stock insuficiente para '$nombreInsumo'. Disponible: $stockActual, requerido: $cantidad_necesaria.");
            }
        }

        // 4) Insertar venta
        $sqlVenta = "
            INSERT INTO ventas (fecha_venta, total_venta, id_mesa, id_usuario, id_cliente, id_sucursal)
            VALUES ('$fecha', '$total', $id_mesa, '$id_usuario', $id_cliente, '$id_sucursal')
        ";
        if (!$db->query($sqlVenta)) throw new Exception("Error al insertar venta: " . $db->error);
        $id_venta = $db->insert_id;

        // 5) Insertar detalle y descontar inventario
        foreach ($_POST['id_plato'] as $i => $id_plato) {
            $cantidadPlatos = floatval($_POST['cantidad'][$i]);
            $precio = floatval($_POST['precio_unitario'][$i]);
            if (empty($id_plato) || $cantidadPlatos <= 0) continue;

            $sqlDetalle = "
                INSERT INTO detalle_venta (id_venta, id_plato, cantidad, precio_unitario)
                VALUES ($id_venta, $id_plato, $cantidadPlatos, $precio)
            ";
            if (!$db->query($sqlDetalle)) throw new Exception("Error al insertar detalle_venta: " . $db->error);

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

                    $qUpd = "UPDATE inventario_materiaprima
                             SET stock = stock - $cantidadUsada
                             WHERE id_insumo = $id_insumo AND id_almacen = $id_almacen";
                    if (!$db->query($qUpd)) throw new Exception("Error al actualizar inventario: " . $db->error);
                }
            }
        }

        // 6) Confirmar transacción
        $db->commit();
        header("Location: ventas.php?agregado=1");
        exit;

    } catch (Exception $e) {
        $db->rollback();
        die("Error al procesar la venta: " . $e->getMessage());
    }
}

// --------------------------------------------------
// EDITAR VENTA
// --------------------------------------------------
if (isset($_POST['editar'])) {
    $id = $_POST['id_venta'];
    $fecha = $_POST['fecha_venta'];
    $total = $_POST['total_venta'];
    $id_mesa = empty($_POST['id_mesa']) ? 'NULL' : "'".$_POST['id_mesa']."'";
    $id_usuario = $_POST['id_usuario'];
    $id_sucursal = $_POST['id_sucursal'];

    
    $id_cliente = isset($_POST['id_cliente']) && $_POST['id_cliente'] !== '' 
        ? intval($_POST['id_cliente']) 
        : 'NULL';

    $sql = "
        UPDATE ventas 
        SET fecha_venta='$fecha',
            total_venta='$total',
            id_mesa=$id_mesa,
            id_usuario='$id_usuario',
            id_cliente=$id_cliente,
            id_sucursal='$id_sucursal'
        WHERE id_venta=$id
    ";
    $db->query($sql);
    header("Location: ventas.php?editado=1");
    exit;
}

// --------------------------------------------------
// ELIMINAR VENTA
// --------------------------------------------------
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->query("DELETE FROM detalle_venta WHERE id_venta=$id");
    $db->query("DELETE FROM ventas WHERE id_venta=$id");
    header("Location: ventas.php?eliminado=1");
    exit;
}
?>
