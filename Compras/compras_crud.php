<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = conectar();
$db->set_charset('utf8mb4');

function back_with(string $type, string $msg): never {
  $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg];
  header('Location: compras.php');
  exit();
}

// ======================= AGREGAR / EDITAR =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Cabecera
  $id_compra    = isset($_POST['id_compra']) ? (int)$_POST['id_compra'] : 0;
  $id_proveedor = isset($_POST['id_proveedor']) ? (int)$_POST['id_proveedor'] : 0;
  $id_sucursal  = (isset($_POST['id_sucursal']) && $_POST['id_sucursal']!=='') ? (int)$_POST['id_sucursal'] : null;
  $fecha_compra = $_POST['fecha_compra'] ?? '';
  $total_compra = isset($_POST['total_compra']) ? (float)$_POST['total_compra'] : 0.0;
  $estado       = $_POST['estado_compra'] ?? 'ACTIVA';

  // Detalle (una línea)
  $id_insumo       = isset($_POST['id_insumo']) ? (int)$_POST['id_insumo'] : 0;
  $cantidad_insumo = isset($_POST['cantidad_insumo']) ? (float)$_POST['cantidad_insumo'] : 0.0;
  $precio_unitario = isset($_POST['precio_unitario']) ? (float)$_POST['precio_unitario'] : 0.0;

  // Validaciones básicas
  if ($id_proveedor <= 0) back_with('error','El proveedor es obligatorio.');
  if (!$fecha_compra || strtotime($fecha_compra)===false) back_with('error','La fecha de compra es inválida.');
  if (!($cantidad_insumo>0)) back_with('error','La cantidad debe ser mayor a 0.');
  if ($precio_unitario < 0) back_with('error','El precio no puede ser negativo.');
  if ($estado!=='ACTIVA' && $estado!=='ANULADA') $estado = 'ACTIVA';

  // Recalcular el total por seguridad: cantidad * precio
  $total_calc = round($cantidad_insumo * $precio_unitario, 2);
  $total_compra = $total_calc;

  try {
    $db->begin_transaction();

    if (isset($_POST['editar'])) {
      if ($id_compra <= 0) back_with('error','ID de compra inválido para editar.');

      // Editar cabecera
      $sql  = "UPDATE compra
               SET id_proveedor=?, id_sucursal=?, fecha_compra=?, total_compra=?, estado_compra=?
               WHERE id_compra=?";
      $stmt = $db->prepare($sql);
      // tipos: i (prov) i (suc) s (fecha) d (total) s (estado) i (id)
      $stmt->bind_param('iisdsi', $id_proveedor, $id_sucursal, $fecha_compra, $total_compra, $estado, $id_compra);
      $stmt->execute();

      // Reemplazar detalle (una línea)
      $stmt = $db->prepare("DELETE FROM detalle_compra WHERE id_compra=?");
      $stmt->bind_param('i', $id_compra);
      $stmt->execute();

      $stmt = $db->prepare("INSERT INTO detalle_compra (id_compra, id_insumo, cantidad_insumo, precio_unitario)
                            VALUES (?, ?, ?, ?)");
      $stmt->bind_param('iidd', $id_compra, $id_insumo, $cantidad_insumo, $precio_unitario);
      $stmt->execute();

      $db->commit();
      back_with('success','Compra actualizada con su detalle.');

    } else {
      // Insertar cabecera
      $sql  = "INSERT INTO compra (id_proveedor, id_sucursal, fecha_compra, total_compra, estado_compra)
               VALUES (?, ?, ?, ?, ?)";
      $stmt = $db->prepare($sql);
      // tipos: i i s d s
      $stmt->bind_param('iisds', $id_proveedor, $id_sucursal, $fecha_compra, $total_compra, $estado);
      $stmt->execute();

      $nuevoId = $db->insert_id;

      // Insertar detalle
      $stmt = $db->prepare("INSERT INTO detalle_compra (id_compra, id_insumo, cantidad_insumo, precio_unitario)
                            VALUES (?, ?, ?, ?)");
      $stmt->bind_param('iidd', $nuevoId, $id_insumo, $cantidad_insumo, $precio_unitario);
      $stmt->execute();

      $db->commit();
      back_with('success',"Compra agregada (ID: {$nuevoId}) con su detalle.");
    }

  } catch (Throwable $e) {
    try { $db->rollback(); } catch (Throwable $ign) {}
    back_with('error','Error de base de datos: '.$e->getMessage());
  }
}

// ======================= ELIMINAR =======================
if (isset($_GET['eliminar'])) {
  $id = (int)$_GET['eliminar'];
  if ($id <= 0) back_with('error','ID inválido.');

  try {
    $db->begin_transaction();

    $stmt = $db->prepare("DELETE FROM detalle_compra WHERE id_compra=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt = $db->prepare("DELETE FROM compra WHERE id_compra=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $db->commit();
    back_with('success','Compra eliminada correctamente.');
  } catch (Throwable $e) {
    try { $db->rollback(); } catch (Throwable $ign) {}
    back_with('error','No se pudo eliminar la compra: '.$e->getMessage());
  }
}

back_with('error','Operación no válida.');
