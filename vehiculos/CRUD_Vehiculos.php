<?php
require_once '../conexion.php';
header('Content-Type: application/json');
$db = conectar();

$response = ['status' => 'error', 'message' => 'Error desconocido'];

// AGREGAR o EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id_vehiculo'] ?? '';
  $placa = $_POST['placa'];
  $marca = $_POST['marca'];
  $modelo = $_POST['modelo'];
  $anio = $_POST['anio'];
  $tipo = $_POST['tipo_vehiculo'];
  $estado = $_POST['estado_vehiculo'];
  $sucursal = $_POST['id_sucursal'];

  if (isset($_POST['editar'])) {
    $query = "UPDATE vehiculos SET 
      placa='$placa', marca='$marca', modelo='$modelo', anio='$anio',
      tipo_vehiculo='$tipo', estado_vehiculo='$estado', id_sucursal='$sucursal'
      WHERE id_vehiculo=$id";
    if ($db->query($query)) {
      $response = ['status' => 'success', 'message' => 'Vehículo actualizado correctamente'];
    }
  } else {
    $query = "INSERT INTO vehiculos (placa, marca, modelo, anio, tipo_vehiculo, estado_vehiculo, id_sucursal)
              VALUES ('$placa', '$marca', '$modelo', '$anio', '$tipo', '$estado', '$sucursal')";
    if ($db->query($query)) {
      $response = ['status' => 'success', 'message' => 'Vehículo agregado correctamente'];
    }
  }
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  if ($db->query("DELETE FROM vehiculos WHERE id_vehiculo=$id")) {
    $response = ['status' => 'success', 'message' => 'Vehículo eliminado correctamente'];
  } else {
    $response = ['status' => 'error', 'message' => 'No se pudo eliminar el vehículo'];
  }
}

echo json_encode($response);
exit;
?>
