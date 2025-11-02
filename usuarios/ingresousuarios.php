<?php
include_once '../conexion.php';

// Validar campos obligatorios
if (empty($_GET['name']) || empty($_GET['email']) || empty($_GET['password']) || empty($_GET['rol'])) {
    echo json_encode(['estado' => 0, 'mensaje' => 'Todos los campos son obligatorios']);
    exit();
}

$nom = filter_input(INPUT_GET, 'name');
$nom = trim($nom);
$nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');

$email = filter_input(INPUT_GET, 'email');
$email = trim($email);
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

$password = filter_input(INPUT_GET, 'password');
$password = trim($password);
$password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');

$rol = filter_input(INPUT_GET, 'rol', FILTER_VALIDATE_INT);

try {
    $db = conectar();

    if (!$db) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Insertar usuario (contraseña sin encriptar)
    $sql = "INSERT INTO usuarios (nombre_usuario, correo_usuario, contraseña_usuario, id_rol) 
            VALUES (?, ?, ?, ?)";

    $stmt = $db->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $db->error);
    }

    $stmt->bind_param("sssi", $nom, $email, $password, $rol);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['estado' => 2, 'mensaje' => 'Usuario guardado correctamente']);
    } else {
        echo json_encode(['estado' => 0, 'mensaje' => 'No se pudo guardar el usuario: ' . $stmt->error]);
    }

    $stmt->close();
    $db->close();

} catch (Exception $e) {
    echo json_encode(['estado' => 0, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
exit();
?>

