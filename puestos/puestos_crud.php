<?php
require_once '../conexion.php';
$db = conectar();

// ================== AGREGAR ==================
if (isset($_POST['agregar'])) {
  $nombre = trim($_POST['nombre_puestos']);
  $descripcion = trim($_POST['descripcion_puestos']);
  $salario = trim($_POST['salario_base_puestos']);

  // Validar duplicado por nombre
  $check = $db->prepare("SELECT * FROM Puestos WHERE nombre_puestos=?");
  $check->bind_param("s", $nombre);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows > 0) {
    header("Location: puestos.php?msg=duplicado");
    exit;
  }

  $stmt = $db->prepare("INSERT INTO Puestos (nombre_puestos, descripcion_puestos, salario_base_puestos) VALUES (?, ?, ?)");
  $stmt->bind_param("ssd", $nombre, $descripcion, $salario);
  $stmt->execute();

  header("Location: puestos.php?msg=agregado");
  exit;
}

// ================== EDITAR ==================
if (isset($_POST['editar'])) {
  $id = $_POST['id_puesto'];
  $nombre = trim($_POST['nombre_puestos']);
  $descripcion = trim($_POST['descripcion_puestos']);
  $salario = trim($_POST['salario_base_puestos']);

  // Evitar duplicados al editar
  $check = $db->prepare("SELECT * FROM Puestos WHERE nombre_puestos=? AND id_puesto<>?");
  $check->bind_param("si", $nombre, $id);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows > 0) {
    header("Location: puestos.php?msg=duplicado");
    exit;
  }

  $stmt = $db->prepare("UPDATE Puestos SET nombre_puestos=?, descripcion_puestos=?, salario_base_puestos=? WHERE id_puesto=?");
  $stmt->bind_param("ssdi", $nombre, $descripcion, $salario, $id);
  $stmt->execute();

  header("Location: puestos.php?msg=editado");
  exit;
}

// ================== ELIMINAR ==================
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  $db->query("DELETE FROM Puestos WHERE id_puesto=$id");
  header("Location: puestos.php?msg=eliminado");
  exit;
}
?>