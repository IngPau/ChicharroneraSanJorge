<?php 
include_once "../conexion.php";

$id = isset($_GET['id_pago_proveedor']) ? $_GET['id_pago_proveedor'] : '';
$conn = conectar();

$resultado = false;
if ($id) {
    $consultaDatos = $conn->prepare("DELETE FROM pagos_proveedor WHERE id_pago_proveedor = ?");
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