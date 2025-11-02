<?php
include_once '../conexion.php';
header('Content-Type: application/json');

// Validar campos obligatorios
if (empty($_POST['nombre'])) {
    echo json_encode(['estado' => 0, 'mensaje' => 'El nombre del rol es obligatorio']);
    exit();
}

$nombre = trim($_POST['nombre']);
$nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

$descripcion = trim($_POST['descripcion'] ?? '');
$descripcion = htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8');

$permisos_seleccionados = $_POST['permisos'] ?? [];

try {
    $db = conectar();
    $db->begin_transaction();

    if (!$db) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // 1. Insertar rol
    $sql_rol = "INSERT INTO roles (nombre_rol, descripcion_rol) VALUES (?, ?)";
    $stmt_rol = $db->prepare($sql_rol);

    if (!$stmt_rol) {
        throw new Exception("Error en la preparación de la consulta: " . $db->error);
    }

    $stmt_rol->bind_param("ss", $nombre, $descripcion);
    $stmt_rol->execute();
    
    $id_rol = $stmt_rol->insert_id; // Obtener el ID del nuevo rol
    $stmt_rol->close();

    // 2. Insertar permisos seleccionados
    if (!empty($permisos_seleccionados)) {
        $sql_permiso = "INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)";
        $stmt_permiso = $db->prepare($sql_permiso);
        
        if (!$stmt_permiso) {
            throw new Exception("Error al preparar consulta de permisos: " . $db->error);
        }
        
        $stmt_permiso->bind_param("ii", $id_rol, $id_permiso);
        
        foreach ($permisos_seleccionados as $id_permiso) {
            $id_permiso = (int)$id_permiso;
            $stmt_permiso->execute();
        }
        $stmt_permiso->close();
    }

    $db->commit();
    echo json_encode(['estado' => 2, 'mensaje' => 'Rol y permisos guardados correctamente']);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['estado' => 0, 'mensaje' => 'Error: ' . $e->getMessage()]);
}

$db->close();
exit();
?>