<?php
include_once "../conexion.php";
header('Content-Type: application/json');

$conn = conectar();
$conn->begin_transaction(); // Iniciar transacción para asegurar atomicidad
$datos = json_decode(file_get_contents('php://input'), true);

$id_rol = $datos['id_rol'];
$nombre = $datos['nombre'];
$descripcion = $datos['descripcion'];
$permisos_seleccionados = $datos['permisos_seleccionados'] ?? [];
$exito = true;
$mensaje = "Rol y permisos actualizados correctamente.";

try {
    // 1. Actualizar datos básicos del rol (nombre, descripción)
    $sql_rol = "UPDATE roles SET nombre_rol = ?, descripcion_rol = ? WHERE id_rol = ?";
    $stmt_rol = $conn->prepare($sql_rol);
    
    if (!$stmt_rol) {
        throw new Exception("Error al preparar la consulta de rol: " . $conn->error);
    }
    
    $stmt_rol->bind_param("ssi", $nombre, $descripcion, $id_rol);
    $stmt_rol->execute();
    $stmt_rol->close();

    // 2. Eliminar todos los permisos actuales para ese rol
    $sql_delete = "DELETE FROM rol_permisos WHERE id_rol = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    
    if (!$stmt_delete) {
        throw new Exception("Error al preparar la consulta de eliminación de permisos: " . $conn->error);
    }
    
    $stmt_delete->bind_param("i", $id_rol);
    $stmt_delete->execute();
    $stmt_delete->close();
    
    // 3. Insertar los nuevos permisos seleccionados
    if (!empty($permisos_seleccionados)) {
        $sql_insert = "INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        
        if (!$stmt_insert) {
            throw new Exception("Error al preparar la consulta de inserción de permisos: " . $conn->error);
        }

        $stmt_insert->bind_param("ii", $id_rol, $id_permiso);
        
        foreach ($permisos_seleccionados as $id_permiso) {
            $id_permiso = (int)$id_permiso; // Asegurar que sea entero
            $stmt_insert->execute();
            if ($stmt_insert->error) {
                throw new Exception("Error al insertar permiso $id_permiso: " . $stmt_insert->error);
            }
        }
        $stmt_insert->close();
    }

    $conn->commit();
    
} catch (Exception $e) {
    $conn->rollback();
    $exito = false;
    $mensaje = "Error en la transacción: " . $e->getMessage();
}

echo json_encode(['estado' => $exito, 'mensaje' => $mensaje]);

$conn->close();
?>