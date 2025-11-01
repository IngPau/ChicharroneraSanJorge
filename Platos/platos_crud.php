<?php
session_start();
require_once '../conexion.php';
$db = conectar();

// ===== AGREGAR PLATO =====
if (isset($_POST['agregar'])) {
  $nombre = $db->real_escape_string($_POST['nombre_plato']);
  $descripcion = $db->real_escape_string($_POST['descripcion_plato']);
  $precio = floatval($_POST['precio_plato']);
  $categoria = intval($_POST['id_categoria']);

  $db->query("
    INSERT INTO platos (nombre_plato, descripcion_plato, precio_plato, id_categoria)
    VALUES ('$nombre', '$descripcion', $precio, $categoria)
  ");

  header("Location: platos.php");
  exit();
}

// ===== EDITAR PLATO =====
if (isset($_POST['editar'])) {
  $id = intval($_POST['id_plato']);
  $nombre = $db->real_escape_string($_POST['nombre_plato']);
  $descripcion = $db->real_escape_string($_POST['descripcion_plato']);
  $precio = floatval($_POST['precio_plato']);
  $categoria = intval($_POST['id_categoria']);

  $db->query("
    UPDATE platos
    SET nombre_plato = '$nombre',
        descripcion_plato = '$descripcion',
        precio_plato = $precio,
        id_categoria = $categoria
    WHERE id_plato = $id
  ");

  header("Location: platos.php");
  exit();
}

// ===== ELIMINAR PLATO =====
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  $db->query("DELETE FROM platos WHERE id_plato = $id");

  header("Location: platos.php");
  exit();
}
?>
