<?php 
include_once "../conexion.php";

$id = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
$conn = conectar();

$resultado = false;

if ($id) {
    // Preparamos la consulta segura para eliminar al usuario
    $consulta = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $consulta->bind_param("i", $id);
    $consulta->execute();

    if ($consulta->affected_rows > 0) {
        $resultado = true;
    } else {
        $resultado = false;
    }

    echo json_encode(['estado' => $resultado]);
    
    $consulta->close();
    $conn->close();
} else {
    echo json_encode(['estado' => false, 'mensaje' => 'ID de usuario no recibido']);
}
?>
