<?php
include_once '../conexion.php';

// Verificar si hay datos
$compra = filter_input(INPUT_GET, 'compra', FILTER_VALIDATE_INT);

if ($compra === false || $compra === null) { 
    echo json_encode(['estado' => 0, 'mensaje' => 'El ID de la compra es obligatorio y debe ser un número entero.']);
    exit();
}

if ($compra <= 0) {
    echo json_encode(['estado' => 0, 'mensaje' => 'El ID de la compra debe ser un número positivo.']);
    exit();
}


$monto = filter_input(INPUT_GET, 'monto', FILTER_VALIDATE_FLOAT);

if ($monto === false || $monto === null) {
    echo json_encode(['estado' => 0, 'mensaje' => 'El monto es obligatorio y debe ser un número válido.']);
    exit();
}

if ($monto < 0) {
    echo json_encode(['estado' => 0, 'mensaje' => 'El monto del pago debe ser positivo.']);
    exit();
}


$pago_str = trim(filter_input(INPUT_GET, 'pago'));

if (empty($pago_str)) {
    echo json_encode(['estado' => 0, 'mensaje' => 'La fecha de pago es obligatoria.']);
    exit();
}

// Validacion formato de la fecha (Ej. YYYY-MM-DD)
$formato_fecha = 'Y-m-d';
$fecha_obj = DateTime::createFromFormat($formato_fecha, $pago_str);

if (!$fecha_obj || $fecha_obj->format($formato_fecha) !== $pago_str) {
    echo json_encode(['estado' => 0, 'mensaje' => 'La fecha de pago no tiene un formato válido (Ej: 2024-10-27).']);
    exit();
}
$fecha_pago = $pago_str;


try {
    $db = conectar();
    
    // Verificar conexión
    if (!$db) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    // Usar prepared statements para mayor seguridad
    $sql = "INSERT INTO pagos_proveedor (id_compra, fecha_pago_proveedor, monto_pago_proveedor) 
            VALUES (?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $db->error);
    }
    
    $stmt->bind_param("isd", $compra, $fecha_pago, $monto);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['estado' => 2, 'mensaje' => 'Pago registrado correctamente']);
    } else {
        echo json_encode(['estado' => 0, 'mensaje' => 'No se pudo registrar el pago: ' . $stmt->error]);
    }
    
    $stmt->close();
    $db->close();
    
} catch (Exception $e) {
    echo json_encode(['estado' => 0, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
exit();
?>