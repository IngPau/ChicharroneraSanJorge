<?php
// editarpermisos.php - Versión de prueba
include_once "../conexion.php";

// Configurar headers primero
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Log para depuración
error_log("=== EDITAR PERMISOS PHP INICIADO ===");

try {
    // Leer input
    $input = file_get_contents('php://input');
    error_log("Input recibido: " . $input);
    
    $datos = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido: ' . json_last_error_msg());
    }
    
    error_log("Datos decodificados: " . print_r($datos, true));
    
    $id_permiso = $datos['id_permiso'] ?? null;
    $nombre = $datos['nombre'] ?? null;
    
    if (!$id_permiso) {
        throw new Exception('ID de permiso no proporcionado');
    }
    
    if (empty($nombre)) {
        throw new Exception('Nombre de permiso vacío');
    }
    
    // Conectar a BD
    $conn = conectar();
    if (!$conn) {
        throw new Exception('Error de conexión a BD');
    }
    
    // Preparar consulta
    $sql = "UPDATE permisos SET nombre_permiso = ? WHERE id_permiso = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparando consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("si", $nombre, $id_permiso);
    
    if (!$stmt->execute()) {
        throw new Exception('Error ejecutando consulta: ' . $stmt->error);
    }
    
    $exito = true;
    $mensaje = "Permiso actualizado correctamente";
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    $exito = false;
    $mensaje = $e->getMessage();
}

// Enviar respuesta
$respuesta = [
    'estado' => $exito,
    'mensaje' => $mensaje,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($respuesta);
error_log("Respuesta enviada: " . json_encode($respuesta));

exit();
?>