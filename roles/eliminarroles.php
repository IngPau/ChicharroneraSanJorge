<?php 
include_once "../conexion.php";
header('Content-Type: application/json');

$id_rol = isset($_GET['id_rol']) ? $_GET['id_rol'] : '';
$conn = conectar();

$resultado = false;

if ($id_rol) {
    $consulta = $conn->prepare("DELETE FROM roles WHERE id_rol = ?");
    
    if ($consulta === false) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al preparar la consulta: ' . $conn->error]);
        exit();
    }
    
    $consulta->bind_param("i", $id_rol);
    $consulta->execute();

    if ($consulta->affected_rows > 0) {
        $resultado = true;
    } else {
        // Podría ser false si el ID no existe
        $resultado = false;
    }

    echo json_encode(['estado' => $resultado]);
    
    $consulta->close();
    $conn->close();
} else {
    echo json_encode(['estado' => false, 'mensaje' => 'ID de rol no recibido']);
}
?>