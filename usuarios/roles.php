<?php
include_once '../conexion.php';

try {
    $db = conectar();

    $sql = "SELECT id_rol, nombre_rol FROM roles";
    $result = $db->query($sql);

    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($roles);
    $db->close();

} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener roles: ' . $e->getMessage()]);
}
?>
