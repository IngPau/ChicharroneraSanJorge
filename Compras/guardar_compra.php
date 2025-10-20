<?php
// Compras/guardar_compra.php
try {
  $pdo = new PDO("odbc:DSN=DW");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Datos del encabezado
  $id_proveedor = $_POST['id_proveedor'] ?? null;
  $fecha_compra = $_POST['fecha_compra'] ?? null;
  $total_compra = $_POST['total_compra'] ?? '0.00';
  $estado       = 'ACTIVA';

  // Detalles
  $ids_insumo = $_POST['id_insumo'] ?? [];
  $cantidades = $_POST['cantidad_insumo'] ?? [];
  $precios    = $_POST['precio_unitario'] ?? [];

  // Validaciones 
  if (!$id_proveedor || !$fecha_compra) {
    throw new Exception('Faltan datos del encabezado.');
  }
  if (!(is_array($ids_insumo) && count($ids_insumo) > 0)) {
    throw new Exception('Debe agregar al menos un insumo.');
  }

  // Transacción
  $pdo->beginTransaction();

  // Insert en Compra
  $sqlCompra = "INSERT INTO Compra (id_proveedor, fecha_compra, total_compra, estado_compra)
                VALUES (?, ?, ?, ?)";
  $stmt = $pdo->prepare($sqlCompra);
  $stmt->execute([$id_proveedor, $fecha_compra, $total_compra, $estado]);

  // id_compra recién generado
  $id_compra = $pdo->lastInsertId();

  // Insert detalles
  $sqlDet = "INSERT INTO Detalle_Compra (id_compra, id_insumo, cantidad_insumo, precio_unitario)
             VALUES (?, ?, ?, ?)";
  $det = $pdo->prepare($sqlDet);

  $n = count($ids_insumo);
  for ($i = 0; $i < $n; $i++) {
    $idInsumo = $ids_insumo[$i] ?? null;
    $cant     = $cantidades[$i] ?? null;
    $precio   = $precios[$i] ?? null;
    if (!$idInsumo || !$cant || $precio === null) continue; // salta filas vacías
    $det->execute([$id_compra, $idInsumo, $cant, $precio]);
  }

  $pdo->commit();
  header('Location: compras.php?ok=1');
  exit;
} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo "Error al guardar la compra: " . $e->getMessage();
}
