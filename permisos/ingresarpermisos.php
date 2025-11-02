<?php
include_once '../conexion.php';
header('Content-Type: application/json');

if (empty($_GET['nombre'])) {
    echo json_encode(['estado' => 0, 'mensaje' => 'El nombre es obligatorio.']);
    exit();
}

$nombre = filter_input(INPUT_GET, 'nombre');
$nombre = trim($nombre);
$nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

try {
    $db = conectar();

    if (!$db) {
        throw new Exception("Error de conexión a la base de datos");
    }

    $sql = "INSERT INTO permisos (nombre_permiso) VALUES (?)";
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error en la preparación: " . $db->error);
    }

    $stmt->bind_param("s", $nombre);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['estado' => 2, 'mensaje' => 'Permiso guardado correctamente']);
    } else {
        $error_msg = ($db->errno == 1062) ? 'Ya existe un permiso con ese nombre.' : 'No se pudo guardar el permiso.';
        echo json_encode(['estado' => 0, 'mensaje' => $error_msg]);
    }

    $stmt->close();
    $db->close();

} catch (Exception $e) {
    echo json_encode(['estado' => 0, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
exit();
?>