<?php
session_start(); // Iniciar la sesión para manejar el estado del usuario

require_once '../conexion.php'; // Ruta al archivo de configuración
$db = conectar();

// Verificar la conexión
if ($db->connect_error) {
    die("Error de conexión: " . $db->connect_error);
} 

// Obtener los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioNombre = $_POST['usuario'];
    $contraseña = $_POST['password'];

    //Evitar inyecciones SQL
    $usuarioNombre = $db->real_escape_string($usuarioNombre);

// Preparar la consulta para obtener el usuario y la contraseña encriptada
$stmt = $db->prepare("SELECT id_usuario, nombre_usuario, contraseña_usuario FROM Usuarios WHERE nombre_usuario = ?");
$stmt->bind_param("s", $usuarioNombre);

// Ejecutar la consulta
$stmt->execute();
// Obtener el resultado
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $user = $resultado->fetch_assoc(); 
        $_SESSION['usuario'] = $user['nombre_usuario'];
        $_SESSION['usuario_id'] = $user['id_usuario'];

        // Redirigir a la página principal (o dashboard)
        header("Location: ../index.php");
        exit();
} else {
    // Usuario no encontrado
    echo "Usuario o contraseña incorrectos.";
}

// Cerrar la declaración
$stmt->close();
}

$db->close();
?>