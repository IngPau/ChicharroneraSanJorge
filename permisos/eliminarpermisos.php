<?php 
include_once "../conexion.php";
header('Content-Type: application/json');

$id_permiso = isset($_GET['id_permiso']) ? $_GET['id_permiso'] : '';
$conn = conectar();

$resultado = false;

if ($id_permiso) {
    try {
        // Primero verificar si el permiso está siendo usado
        $check_sql = "SELECT COUNT(*) as count FROM rol_permisos WHERE id_permiso = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $id_permiso);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();
        
        if ($row['count'] > 0) {
            echo json_encode(['estado' => false, 'mensaje' => 'No se puede eliminar: el permiso está asignado a uno o más roles.']);
            exit();
        }
        
        $check_stmt->close();

        // Eliminar el permiso
        $consulta = $conn->prepare("DELETE FROM permisos WHERE id_permiso = ?");
        
        if ($consulta === false) {
            throw new Exception('Error al preparar la consulta: ' . $conn->error);
        }
        
        $consulta->bind_param("i", $id_permiso);
        $consulta->execute();

        if ($consulta->affected_rows > 0) {
            $resultado = true;
            $mensaje = "Permiso eliminado correctamente.";
        } else {
            $resultado = false;
            $mensaje = "No se encontró el permiso o ya fue eliminado.";
        }

        $consulta->close();
        
    } catch (Exception $e) {
        $resultado = false;
        $mensaje = "Error: " . $e->getMessage();
    }

    echo json_encode(['estado' => $resultado, 'mensaje' => $mensaje]);
    
    $conn->close();
} else {
    echo json_encode(['estado' => false, 'mensaje' => 'ID de permiso no recibido']);
}
?>