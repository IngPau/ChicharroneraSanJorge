<?php
include_once '../conexion.php';

// Verificar si hay datos
if(empty($_GET['name'])) {
    echo json_encode(['estado' => 0, 'mensaje' => 'El nombre es obligatorio']);
    exit();
}

$nom = filter_input(INPUT_GET, 'name'); 
$nom = trim($nom);
$nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');

$email = filter_input(INPUT_GET, 'email');
$email = trim($email);
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

$phone = filter_input(INPUT_GET, 'phone');
$phone = trim($phone);
$phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');

$address = filter_input(INPUT_GET, 'address');
$address = trim($address);
$address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');

try {
    $db = conectar();
    
    // Verificar conexión
    if (!$db) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    // Usar prepared statements para mayor seguridad
    $sql = "INSERT INTO proveedores (nombre_proveedor, correo_proveedor, telefono_proveedor, direccion_proveedor) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $db->error);
    }
    
    $stmt->bind_param("ssss", $nom, $email, $phone, $address);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['estado' => 2, 'mensaje' => 'Proveedor guardado correctamente']);
    } else {
        echo json_encode(['estado' => 0, 'mensaje' => 'No se pudo guardar el proveedor: ' . $stmt->error]);
    }
    
    $stmt->close();
    $db->close();
    
} catch (Exception $e) {
    echo json_encode(['estado' => 0, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
exit();
?>