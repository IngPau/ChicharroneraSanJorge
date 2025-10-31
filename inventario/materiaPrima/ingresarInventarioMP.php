<?php
include_once '../../conexion.php';

$conn = conectar();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $materiaPrima = trim($_POST['MateriaP']);
    $cantidad = isset($_POST['stock']) ? (float)$_POST['stock'] : 0;
    $unidadMedida = trim($_POST['uMedida']);
    $stockMinimo = isset($_POST['stockMinimo']) ? (float)$_POST['stockMinimo'] : 0;
    $sucursalSeleccionada = $_POST['sucursalSeleccionada']; 
    $conn->begin_transaction();

    try {
        // Verificar existencia de la materia prima
        $codigoMateriaPrima = verificarExistencia("MateriaPrima", "id_materia_prima", "nombre_insumos", $materiaPrima);

        $consultaAlmacen = $conn->prepare("SELECT al.id_almacen 
                                           FROM Almacenes_Sucursal al
                                           JOIN Sucursales s ON al.id_sucursal = s.id_sucursal
                                           WHERE s.nombre_sucursal = ?");
        $consultaAlmacen->bind_param('s', $sucursalSeleccionada);
        $consultaAlmacen->execute();
        $consultaAlmacen->bind_result($codigoAlmacen);
        if (!$consultaAlmacen->fetch()) {
            $consultaAlmacen->close();
            $conn->rollback();
            header('Location: inventarioMP.php?status=error');
            exit();
        }
        $consultaAlmacen->close();

        // Verificar si ya existe en inventario
        $consultaInventario = $conn->prepare("SELECT stock 
                                              FROM Inventario_MateriaPrima 
                                              WHERE id_almacen = ? AND id_insumo = ?");
        $consultaInventario->bind_param('ii', $codigoAlmacen, $codigoMateriaPrima);
        $consultaInventario->execute();
        $resultado = $consultaInventario->get_result();

        if ($resultado->num_rows > 0) {
            // Si existe, actualizar stock
            $fila = $resultado->fetch_assoc();
            $nuevaCantidad = $fila['stock'] + $cantidad;

            $actualizarInventario = $conn->prepare("UPDATE Inventario_MateriaPrima 
                                                    SET stock = ?, unidad_medida = ?, cantidad_minima = ?
                                                    WHERE id_almacen = ? AND id_insumo = ?");
            $actualizarInventario->bind_param("dsdii", $nuevaCantidad, $unidadMedida, $stockMinimo, $codigoAlmacen, $codigoMateriaPrima);
            $actualizarInventario->execute();

            if ($actualizarInventario->affected_rows > 0) {
                $conn->commit();
                header('Location: inventarioMP.php?status=success');
                exit();
            } else {
                throw new Exception("No se pudo actualizar el inventario.");
            }

        } else {
            $ingresarInventario = $conn->prepare("INSERT INTO Inventario_MateriaPrima 
                                                  (id_almacen, id_insumo, stock, unidad_medida, cantidad_minima) 
                                                  VALUES (?,?,?,?,?)");
            $ingresarInventario->bind_param("iidsd", $codigoAlmacen, $codigoMateriaPrima, $cantidad, $unidadMedida, $stockMinimo);
            $ingresarInventario->execute();

            if ($ingresarInventario->affected_rows > 0) {
                $conn->commit();
                header('Location: inventarioMP.php?status=success');
                exit(); 
            } else {
                throw new Exception("No se pudo añadir al inventario.");
            }
        }

    } catch (Exception $e) {
        $conn->rollback();
        header('Location: inventarioMP.php?status=error');
        exit();
    }
}

   

function verificarExistencia($tabla, $campoId, $atributo, $valor){
    global $conn;

    $sql = "SELECT $campoId FROM $tabla WHERE $atributo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Si existe, devuelve el ID del atributo asignado
        $id = $resultado->fetch_assoc();
        $stmt->close();
        return $id[$campoId];
    } else {
        $stmt->close();
        // En caso contrario, se debe hacer un nuevo insert 
        $insercionSQL = $conn->prepare("INSERT INTO $tabla($atributo) VALUES (?)");
        $insercionSQL->bind_param("s", $valor);
        $insercionSQL->execute();
        // Obtener el ID del nuevo registro insertado
        $insertedId = $conn->insert_id;
        $insercionSQL->close();
        return $insertedId;
    }
}
?>