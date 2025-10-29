<?php 

include_once "../conexion.php";

$conn = conectar();

$datos= json_decode(file_get_contents('php://input'), true);
$dpi = $datos['id'];
$resultado = false;

if (isset($datos['compra'])){
    $consultaDatos = $conn->prepare("UPDATE pagos_proveedor SET id_compra = ? WHERE id_pago_proveedor = ?");
    $consultaDatos->bind_param("si", $datos['compra'], $dpi);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
}
if (isset($datos['pago'])){
    $consultaDatos = $conn->prepare("UPDATE pagos_proveedor SET fecha_pago_proveedor = ? WHERE id_pago_proveedor = ?");
    $consultaDatos->bind_param("si", $datos['pago'], $dpi);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
}
if (isset($datos['monto'])){
    $consultaDatos = $conn->prepare("UPDATE pagos_proveedor SET monto_pago_proveedor = ? WHERE id_pago_proveedor = ?");
    $consultaDatos->bind_param("di", $datos['monto'], $dpi);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
}
    echo json_encode(['estado' => $resultado]);
    $conn->close();
?> 