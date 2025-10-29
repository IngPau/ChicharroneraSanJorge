<?php
include_once '../../conexion.php';

$conn = conectar();

if ($_SERVER["REQUEST_METHOD"]== 'POST') {
    // Obtener los datos del formulario
    $mobiliario = trim($_POST['mobiliario']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);
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
            header('Location: inventarioMobiliario.php?status=error');
            exit();
        }
        $consultaAlmacen->close();
        
        $codigoCategoria = verificarExistencia('Categoria_Mobiliario','id_categoria_mobiliario', 'nombre_categoria', $categoria);
        $idMobiliario = verificarMobiliario($mobiliario, $descripcion, $codigoCategoria);

        $consultaInventario = $conn->prepare("SELECT stock FROM Inventario_Mobiliario 
                                                WHERE id_almacen = ? AND id_mobiliario = ?");
        $consultaInventario->bind_param('ii', $codigoAlmacen, $idMobiliario);   
        $consultaInventario->execute();
        $resultado = $consultaInventario->get_result();
        if ($resultado->num_rows > 0 ){
            $fila = $resultado->fetch_assoc();
            $nuevaCantidad = $fila['stock'] + $cantidad;
            $actualizarInventario = $conn->prepare("UPDATE Inventario_Mobiliario SET stock = ? 
            WHERE id_almacen = ? AND id_mobiliario = ?");
            $actualizarInventario->bind_param("iii", $nuevaCantidad, $codigoAlmacen, $idMobiliario);
            $actualizarInventario->execute();
            if ($actualizarInventario->affected_rows > 0) {
                $conn->commit();
                header('Location: inventarioMobiliario.php?status=success');
                exit(); 
            } else {
                throw new Exception("No se pudo registrar la compra.");
            }

        } else {
            $ingresarInventario = $conn->prepare("INSERT INTO Inventario_Mobiliario (id_almacen, id_mobiliario, stock) VALUES (?,?,?)");
            $ingresarInventario->bind_param("iii", $codigoAlmacen, $idMobiliario, $cantidad);
            $ingresarInventario->execute();
            if ($ingresarInventario->affected_rows > 0) {
                $conn->commit();
                header('Location: inventarioMobiliario.php?status=success');
                exit(); 
            } else {
                throw new Exception("No se pudo registrar la compra.");
            }

        }
    } catch (Exception $e) {
        $conn->rollback();
        header('Location: inventarioMobiliario.php?status=error');
        exit();
    } 
}

function verificarExistencia($tabla, $campoId, $atributo, $valor){
    global $conn;
    // Consultar si el atributo ya existe en la tabla
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
        // En caso contrario, se debe hacer un nuevo insert en la tabla correspondiente
        $insercionSQL = $conn->prepare("INSERT INTO $tabla($atributo) VALUES (?)");
        $insercionSQL->bind_param("s", $valor);
        $insercionSQL->execute();
        // Obtener el ID del nuevo registro insertado
        $insertedId = $conn->insert_id;
        $insercionSQL->close();
        return $insertedId;
    }
}

function verificarMobiliario($nombreMobiliario, $descripcion, $idcategoria){
    global $conn;
    $consultaMobiliario = $conn->prepare(("SELECT m.id_mobiliario FROM Mobiliario m
                                           WHERE m.nombre_mobiliario= ? AND m.descripcion=?
                                           AND m.id_categoria_mobiliario = ?"));
    $consultaMobiliario->bind_param("ssi",$nombreMobiliario,$descripcion, $idcategoria);
    $consultaMobiliario->execute();
    $resultado = $consultaMobiliario->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $consultaMobiliario->close();
        return $fila["id_mobiliario"];
    } 
    else {
        $consultaMobiliario->close();
        $ingresarMobiliario = $conn->prepare("
            INSERT INTO Mobiliario (nombre_mobiliario, descripcion, id_categoria_mobiliario)
            VALUES (?, ?, ?)
        ");
        $ingresarMobiliario->bind_param("ssi", $nombreMobiliario, $descripcion, $idcategoria);
        if ($ingresarMobiliario->execute()) {
            $nuevoID = $ingresarMobiliario->insert_id; 
            $ingresarMobiliario->close();
            return $nuevoID;
        } else {
            $ingresarMobiliario->close();
            return false; 
        }
    }
}

?>