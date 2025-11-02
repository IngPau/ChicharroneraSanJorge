<?php

session_set_cookie_params([
    'lifetime' => 0, 
    'path' => '/', 
    'domain' => '', 
    'secure' => false,
    'httponly' => true 
]);

session_start();

require_once '../conexion.php';
$db = conectar();

if ($db->connect_error) {
    die("Error de conexión: " . $db->connect_error);
} 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioNombre = $_POST['usuario'];
    $contraseña = $_POST['password'];

    $usuarioNombre = $db->real_escape_string($usuarioNombre);

    // CORRECCIÓN: Seleccionar también el id_rol
    $stmt = $db->prepare("SELECT id_usuario, nombre_usuario, contraseña_usuario, id_rol FROM Usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $usuarioNombre);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc(); 
        $contraseña_almacenada = $user['contraseña_usuario'];

        if ($contraseña === $contraseña_almacenada) {
            // CORRECCIÓN: Guardar el id_rol en la sesión
            $_SESSION['usuario'] = $user['nombre_usuario'];
            $_SESSION['usuario_id'] = $user['id_usuario'];
            $_SESSION['id_rol'] = $user['id_rol']; // ← ESTA LÍNEA ES CLAVE

            header("Location: ../index.php");
            exit();
        } else {
            echo "Usuario o contraseña incorrectos.";
        }
    } else {
        echo "Usuario o contraseña incorrectos.";
    }

    $stmt->close();
}

$db->close();
?>