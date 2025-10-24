<?php
require_once '../conexion.php';
$db = conectar();

// Agregar
if (isset($_POST['agregar'])) {
  $id_empleado = $_POST['id_empleado'];
  $año = $_POST['año'];
  $mes = $_POST['mes'];
  $sueldo = $_POST['sueldo_base'];

  $db->query("INSERT INTO Nomina (id_empleado, año, mes, sueldo_base)
              VALUES ('$id_empleado', '$año', '$mes', '$sueldo')");
  header("Location: planilla.php?mensaje=agregado");
  exit;
}

// Editar
if (isset($_POST['editar'])) {
  $id = $_POST['id_nomina'];
  $id_empleado = $_POST['id_empleado'];
  $año = $_POST['año'];
  $mes = $_POST['mes'];
  $sueldo = $_POST['sueldo_base'];

  $db->query("UPDATE Nomina 
              SET id_empleado='$id_empleado', año='$año', mes='$mes', sueldo_base='$sueldo'
              WHERE id_nomina='$id'");
  header("Location: planilla.php?mensaje=editado");
  exit;
}

// Eliminar
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  $db->query("DELETE FROM Nomina WHERE id_nomina=$id");
  header("Location: planilla.php?mensaje=eliminado");
  exit;
}
?>