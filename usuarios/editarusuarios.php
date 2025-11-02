<?php
include_once "../conexion.php";

$conn = conectar();

$datos = json_decode(file_get_contents('php://input'), true);
$id_usuario = $datos['id'];
$resultado = false;
$mensaje = "";

// Al inicio del archivo, después de obtener los datos
error_log("Datos recibidos: " . print_r($datos, true));

// Verificar que el usuario existe
$consulta_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ?");
$consulta_check->bind_param("i", $id_usuario);
$consulta_check->execute();
$consulta_check->store_result();

if ($consulta_check->num_rows === 0) {
    echo json_encode(['estado' => false, 'mensaje' => 'Usuario no encontrado']);
    exit();
}
$consulta_check->close();

// 🔹 Actualizar nombre de usuario
if (isset($datos['name'])) {
    $consulta = $conn->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id_usuario = ?");
    $consulta->bind_param("si", $datos['name'], $id_usuario);
    if ($consulta->execute()) {
        $resultado = true;
    }
    $consulta->close();
}

// 🔹 Actualizar correo
if (isset($datos['email'])) {
    $consulta = $conn->prepare("UPDATE usuarios SET correo_usuario = ? WHERE id_usuario = ?");
    $consulta->bind_param("si", $datos['email'], $id_usuario);
    if ($consulta->execute()) {
        $resultado = true;
    }
    $consulta->close();
}

// 🔹 Actualizar contraseña (solo si se proporcionó una nueva)
if (isset($datos['password']) && !empty($datos['password'])) {
    $consulta = $conn->prepare("UPDATE usuarios SET contraseña_usuario = ? WHERE id_usuario = ?");
    $consulta->bind_param("si", $datos['password'], $id_usuario);
    if ($consulta->execute()) {
        $resultado = true;
    }
    $consulta->close();
}

// 🔹 Actualizar rol (ahora recibimos directamente el ID del rol)
if (isset($datos['rol'])) {
    $consulta = $conn->prepare("UPDATE usuarios SET id_rol = ? WHERE id_usuario = ?");
    $consulta->bind_param("ii", $datos['rol'], $id_usuario);
    if ($consulta->execute()) {
        $resultado = true;
    }
    $consulta->close();
}

echo json_encode(['estado' => $resultado, 'mensaje' => $resultado ? 'Actualizado correctamente' : 'No se realizaron cambios']);

$conn->close();
?>
