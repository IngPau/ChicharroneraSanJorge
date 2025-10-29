<?php 

include_once "../conexion.php";

$conn = conectar();

$datos= json_decode(file_get_contents('php://input'), true);
$dpi = $datos['id'];
$resultado = false;

if (isset($datos['name'])){
    $consultaDatos = $conn->prepare("UPDATE proveedores SET nombre_proveedor = ? WHERE id_proveedor = ?");
    $consultaDatos->bind_param("si", $datos['name'], $dpi);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
}
if (isset($datos['email'])){
    $consultaDatos = $conn->prepare("UPDATE proveedores SET correo_proveedor = ? WHERE id_proveedor = ?");
    $consultaDatos->bind_param("si", $datos['email'], $dpi);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
}
if (isset($datos['phone'])){
    $consultaDatos = $conn->prepare("UPDATE proveedores SET telefono_proveedor = ? WHERE id_proveedor = ?");
    $consultaDatos->bind_param("si", $datos['phone'], $dpi);
    $consultaDatos->execute();
    if ($consultaDatos->affected_rows === 0) {
        $resultado = false;
    } else {
        $resultado = true;
    }
}
if (isset($datos['address'])){
    $consultaDatos = $conn->prepare("UPDATE proveedores SET direccion_proveedor = ? WHERE id_proveedor = ?");
    $consultaDatos->bind_param("si", $datos['address'], $dpi);
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