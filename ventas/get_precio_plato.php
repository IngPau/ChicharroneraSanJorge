<?php
require_once '../conexion.php';
$db = conectar();

$id = $_GET['id'] ?? null;

if ($id) {
  $res = $db->query("SELECT precio_plato FROM platos WHERE id_plato = $id");
  $row = $res->fetch_assoc();
  echo $row['precio_plato'];
}
?>
