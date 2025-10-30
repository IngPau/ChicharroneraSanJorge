<?php
include_once '../../conexion.php';

$conn = conectar();

$data = json_decode(file_get_contents('php://input'), true);

$codigo = (int) $data['codigo'];

$eliminarInventario = $conn->prepare("DELETE FROM Inventario_MateriaPrima WHERE id_inventario = ?");
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