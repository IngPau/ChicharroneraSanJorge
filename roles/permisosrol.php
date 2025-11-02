<?php
include_once "../conexion.php";
header('Content-Type: application/json');

$id_rol = filter_input(INPUT_GET, 'id_rol', FILTER_VALIDATE_INT);
$conn = conectar();

$data = ['rol' => null, 'permisos' => []];

try {
    // Si id_rol es 0, solo devolvemos permisos sin datos de rol
    if ($id_rol > 0) {
        // Obtener datos del rol si existe
        $sql_rol = "SELECT id_rol, nombre_rol, descripcion_rol FROM roles WHERE id_rol = ?";
        $stmt_rol = $conn->prepare($sql_rol);
        $stmt_rol->bind_param("i", $id_rol);
        $stmt_rol->execute();
        $resultado_rol = $stmt_rol->get_result();
        $data['rol'] = $resultado_rol->fetch_assoc();
        $stmt_rol->close();
    }

    // Obtener todos los permisos
    $sql_permisos = "
        SELECT 
            p.id_permiso, 
            p.nombre_permiso, 
            IF(rp.id_permiso IS NOT NULL AND rp.id_rol = ?, 1, 0) AS asignado
        FROM permisos p
        LEFT JOIN rol_permisos rp ON p.id_permiso = rp.id_permiso AND rp.id_rol = ?
        ORDER BY p.nombre_permiso";

    $stmt_permisos = $conn->prepare($sql_permisos);
    $stmt_permisos->bind_param("ii", $id_rol, $id_rol);
    $stmt_permisos->execute();
    $resultado_permisos = $stmt_permisos->get_result();

    while ($fila = $resultado_permisos->fetch_assoc()) {
        $data['permisos'][] = $fila;
    }
    $stmt_permisos->close();

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(['rol' => null, 'permisos' => null, 'mensaje' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>