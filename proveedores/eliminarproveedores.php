<?php 
include_once "../conexion.php";

$id = isset($_GET['id_proveedor']) ? $_GET['id_proveedor'] : '';
$conn = conectar();

$resultado = false;
if ($id) {
    $consultaDatos = $conn->prepare("DELETE FROM proveedores WHERE id_proveedor = ?");
    $consultaDatos->bind_param(("i"), $id);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
    echo json_encode(['estado' => $resultado]);
}

?> 