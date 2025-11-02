<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

register_shutdown_function(function () {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
    http_response_code(500);
    echo json_encode([
      'error' => 'Fallo interno',
      'mensaje' => $e['message'],
      'linea' => $e['line'],
      'archivo' => basename($e['file'])
    ], JSON_UNESCAPED_UNICODE);
  }
});


// 4) Limpiar cualquier salida previa accidental (BOM/espacios)
if (ob_get_level() === 0) { ob_start(); }


require_once '../conexion.php';
$cn = conectar(); // <-- devuelve mysqli

require_once '../seguridad.php';
require_once '../util_db.php';

header('Content-Type: application/json; charset=utf-8');

// Fuerza errores como excepciones en mysqli:
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Charset recomendado
$cn->set_charset('utf8mb4');

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Método no permitido']);
  exit;
}

// JSON de entrada
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload) || !isset($payload['compras']) || !is_array($payload['compras'])) {
  http_response_code(400);
  echo json_encode(['error' => 'JSON inválido: se esperaba { compras: [...] }']);
  exit;
}

try {
  // Transacción
  $cn->begin_transaction();

  // Preparados reutilizables
  $stmtCompra = $cn->prepare("
    INSERT INTO compra (id_proveedor, id_sucursal, fecha_compra, total_compra, estado_compra)
    VALUES (?, ?, ?, ?, 'ACTIVA')
  ");
  // i = int, d = double, s = string
  // id_proveedor (i), id_sucursal (i), fecha_compra (s), total_compra (d)
  $stmtDet = $cn->prepare("
    INSERT INTO detalle_compra (id_compra, id_insumo, cantidad_insumo, precio_unitario)
    VALUES (?, ?, ?, ?)
  ");
  // id_compra (i), id_insumo (i), cantidad_insumo (d), precio_unitario (d)

  $stmtAlm = $cn->prepare("
    SELECT id_almacen
    FROM almacenes_sucursal
    WHERE id_sucursal = ?
    ORDER BY id_almacen ASC
    LIMIT 1
  ");

  $stmtInvSel = $cn->prepare("
    SELECT id_inventario, stock
    FROM inventario_materiaprima
    WHERE id_almacen = ? AND id_insumo = ?
    LIMIT 1
  ");

  $stmtInvUpd = $cn->prepare("
    UPDATE inventario_materiaprima
    SET stock = stock + ?
    WHERE id_inventario = ?
  ");

  $stmtInvIns = $cn->prepare("
    INSERT INTO inventario_materiaprima (id_almacen, id_insumo, stock, cantidad_minima, unidad_medida)
    VALUES (?, ?, ?, 0, ?)
  ");

  $stmtUnidad = $cn->prepare("
    SELECT unidad_medida
    FROM materiaprima
    WHERE id_materia_prima = ?
    LIMIT 1
  ");

  $idsCreados = [];

  foreach ($payload['compras'] as $c) {
    if (
      !isset($c['id_proveedor'], $c['id_sucursal'], $c['fecha_compra'], $c['detalles']) ||
      !is_array($c['detalles']) || count($c['detalles']) === 0
    ) {
      throw new RuntimeException('Estructura de compra inválida');
    }

    $idProveedor = (int)$c['id_proveedor'];
    $idSucursal  = (int)$c['id_sucursal'];

    $ts = strtotime((string)$c['fecha_compra']);
    if ($ts === false) {
      throw new RuntimeException('fecha_compra inválida');
    }
    $fechaCompra = date('Y-m-d', $ts);

    // Calcular total
    $total = 0.0;
    foreach ($c['detalles'] as $d) {
      if (!isset($d['id_insumo'], $d['cantidad_insumo'], $d['precio_unitario'])) {
        throw new RuntimeException('Detalle incompleto');
      }
      $idInsumo = (int)$d['id_insumo'];
      $cant     = (float)$d['cantidad_insumo'];
      $precio   = (float)$d['precio_unitario'];

      if ($idInsumo <= 0 || $cant < 0 || $precio < 0) {
        throw new RuntimeException('Detalle con valores no válidos');
        }
      $total += $cant * $precio;
    }
    $total = round($total, 2);

    // Insert compra
    $stmtCompra->bind_param('iisd', $idProveedor, $idSucursal, $fechaCompra, $total);
    $stmtCompra->execute();
    $idCompra = (int)$cn->insert_id;
    $idsCreados[] = $idCompra;

    // Insert detalles
    foreach ($c['detalles'] as $d) {
      $idInsumo = (int)$d['id_insumo'];
      $cant     = (float)$d['cantidad_insumo'];
      $precio   = (float)$d['precio_unitario'];

      $stmtDet->bind_param('iidd', $idCompra, $idInsumo, $cant, $precio);
      $stmtDet->execute();
    }

    // Buscar almacén por sucursal
    $stmtAlm->bind_param('i', $idSucursal);
    $stmtAlm->execute();
    $resAlm = $stmtAlm->get_result();
    $alm = $resAlm->fetch_assoc();
    if (!$alm) {
      throw new RuntimeException("No hay almacén asociado a la sucursal {$idSucursal}");
    }
    $idAlmacen = (int)$alm['id_almacen'];

    // Actualización de inventario
    foreach ($c['detalles'] as $d) {
      $idInsumo = (int)$d['id_insumo'];
      $cant     = (float)$d['cantidad_insumo'];

      // ¿Existe inventario?
      $stmtInvSel->bind_param('ii', $idAlmacen, $idInsumo);
      $stmtInvSel->execute();
      $resInv = $stmtInvSel->get_result();
      $inv = $resInv->fetch_assoc();

      if ($inv) {
        $idInventario = (int)$inv['id_inventario'];
        $stmtInvUpd->bind_param('di', $cant, $idInventario);
        $stmtInvUpd->execute();
      } else {
        // Unidad de medida desde catálogo (puede ser NULL)
        $stmtUnidad->bind_param('i', $idInsumo);
        $stmtUnidad->execute();
        $resUm = $stmtUnidad->get_result();
        $rowUm = $resUm->fetch_assoc();
        $unidad = $rowUm ? (string)$rowUm['unidad_medida'] : null;

        $stmtInvIns->bind_param('iids', $idAlmacen, $idInsumo, $cant, $unidad);
        $stmtInvIns->execute();
      }
    }
  }

  $cn->commit();
  echo json_encode(['ok' => true, 'ids' => $idsCreados], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  // Si algo falla, rollback
  try { $cn->rollback(); } catch (Throwable $ignored) {}
  http_response_code(500);
  echo json_encode([
    'error' => 'No se pudo registrar la(s) compra(s)',
    'mensaje' => $e->getMessage()
  ], JSON_UNESCAPED_UNICODE);
}
