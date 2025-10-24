<?php
require_once '../conexion.php';
$db = conectar();

$db->begin_transaction();

try {
  if (!isset($_POST['compras']) || !is_array($_POST['compras'])) {
    throw new Exception("No hay compras para procesar.");
  }

  foreach ($_POST['compras'] as $compra) {
    // Encabezado
    $id_sucursal  = (int)($compra['id_sucursal'] ?? 0);
    $id_proveedor = (int)($compra['id_proveedor'] ?? 0);
    $fecha        = $compra['fecha'] ?? date('Y-m-d');
    $total        = (float)($compra['total'] ?? 0);

    // Inserta compra
    $stmt = $db->prepare("INSERT INTO compras (id_sucursal, id_proveedor, fecha_compra, total) VALUES (?,?,?,?)");
    $stmt->bind_param('iisd', $id_sucursal, $id_proveedor, $fecha, $total);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $id_compra = $stmt->insert_id;

    // Detalles
    if (!empty($compra['items']) && is_array($compra['items'])) {
      $stmtDet = $db->prepare("
        INSERT INTO compra_detalle (id_compra, id_insumo, cantidad, precio_unitario, subtotal)
        VALUES (?,?,?,?,?)
      ");
      foreach ($compra['items'] as $it) {
        $id_insumo = (int)($it['id_insumo'] ?? 0);
        $cant      = (float)($it['cantidad'] ?? 0);
        $precio    = (float)($it['precio'] ?? 0);
        $subtotal  = $cant * $precio;
        $stmtDet->bind_param('iiidd', $id_compra, $id_insumo, $cant, $precio, $subtotal);
        if (!$stmtDet->execute()) throw new Exception($stmtDet->error);
      }
    }
  }

  $db->commit();
  header('Location: compras.php?ok=1');
  exit;

} catch (Exception $e) {
  $db->rollback();
  http_response_code(500);
  echo "Error guardando compras: " . htmlspecialchars($e->getMessage());
}
