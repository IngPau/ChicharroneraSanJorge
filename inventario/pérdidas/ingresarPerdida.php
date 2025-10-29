<?php
include_once '../../conexion.php';

$conn = conectar();

if ($_SERVER["REQUEST_METHOD"]== 'POST') {
    // Obtener los datos del formulario
    $codigoMateriaPrima = trim($_POST['materiaPrima']);
    $motivo = trim($_POST['motivo']);
    $fecha = trim($_POST['fecha']);
    $cantidad = $_POST['cantidad'];
    $sucursalSeleccionada = $_POST['sucursalSeleccionada'];    
    $conn->begin_transaction();
    try {
        $consultaAlmacen = $conn->prepare(("SELECT al.id_almacen FROM Almacenes_Sucursal al
                                            JOIN Sucursales s ON al.id_sucursal=s.id_sucursal
                                            WHERE s.nombre_sucursal= ?"));
        $consultaAlmacen->bind_param('s', $sucursalSeleccionada);
        $consultaAlmacen->execute();
        $consultaAlmacen->bind_result($codigoAlmacen);
        if (!$consultaAlmacen->fetch()) {
            $consultaAlmacen->close();
            $conn->rollback();
            header('Location: inventarioPerdidas.php?status=error');
            exit();
        }
        $consultaAlmacen->close();
        restarInventario($conn, $codigoMateriaPrima, $codigoAlmacen, $cantidad);

        $ingresarInventario = $conn->prepare("INSERT INTO Perdidas (id_almacen, id_materia_prima, cantidad, fecha, motivo) VALUES (?,?,?,?,?)");
        $ingresarInventario->bind_param("iidss", $codigoAlmacen, $codigoMateriaPrima,$cantidad,$fecha,$motivo);
        $ingresarInventario->execute();
        if ($ingresarInventario->affected_rows > 0) {
            $conn->commit();
            header('Location: inventarioPerdidas.php?status=success');
            exit(); 
        } else {
            throw new Exception("No se pudo registrar la pérdida.");
        }        
    } catch (Exception $e) {
        $conn->rollback();
        if (strpos($e->getMessage(), 'Stock insuficiente') !== false) {
            header('Location: inventarioPerdidas.php?status=error&reason=stock');
        } else {
            header('Location: inventarioPerdidas.php?status=error&reason=general');
        }
        exit();
    }
}    

function restarInventario($conn, $idMateriaPrima, $idAlmacen, $cantidadARestar) {
    $consulta = $conn->prepare("
        SELECT stock 
        FROM Inventario_MateriaPrima 
        WHERE id_insumo = ? AND id_almacen = ?
    ");
    $consulta->bind_param("ii", $idMateriaPrima, $idAlmacen);
    $consulta->execute();
    $resultado = $consulta->get_result();
    if ($resultado->num_rows === 0) {
        throw new Exception("El inventario para la materia prima especificada no existe en el almacén dado.");
    }
    $fila = $resultado->fetch_assoc();
    $cantidadActual = (float)$fila['stock'];

    // Verificar que haya suficiente stock
    if ($cantidadActual < $cantidadARestar) {
        throw new Exception("Stock insuficiente. Solo hay $cantidadActual unidades disponibles.");
    }

    // Actualizar cantidad
    $nuevaCantidad = $cantidadActual - $cantidadARestar;
    $actualizar = $conn->prepare("
        UPDATE Inventario_MateriaPrima 
        SET stock = ? 
        WHERE id_insumo = ? AND id_almacen = ?
    ");
    $actualizar->bind_param("dii", $nuevaCantidad, $idMateriaPrima, $idAlmacen);
    $actualizar->execute();
    $actualizar->close();
}

?>