<?php
include_once '../../conexion.php';

$conn = conectar();

$data = json_decode(file_get_contents('php://input'), true);

$codigo = (int) $data['codigo'];

$eliminarInventario = $conn->prepare("DELETE FROM Perdidas WHERE id_perdida = ?");
$eliminarInventario->bind_param('i', $codigo);
$eliminarInventario->execute();
if ($eliminarInventario->affected_rows === 0) {
    $estado = 0;
} else {
    $estado = 1;
}
$eliminarInventario->close();
echo json_encode(['estado' => $estado]);

$conn->close();
?>