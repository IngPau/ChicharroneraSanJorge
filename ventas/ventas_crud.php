<?php
require_once '../conexion.php';
$db = conectar();

// Agregar venta
if (isset($_POST['agregar'])) {
  $fecha = $_POST['fecha_venta'];
  $total = $_POST['total_venta'];
  $id_mesa = empty($_POST['id_mesa']) ? 'NULL' : "'".$_POST['id_mesa']."'";
  $id_usuario = $_POST['id_usuario'];
  $id_sucursal = $_POST['id_sucursal']; // 🔹 Nuevo campo

  // Insertar venta
  $sql = "INSERT INTO ventas (fecha_venta, total_venta, id_mesa, id_usuario, id_sucursal)
          VALUES ('$fecha', '$total', $id_mesa, '$id_usuario', '$id_sucursal')";
  $db->query($sql);
  $id_venta = $db->insert_id;

  // Insertar detalle
  foreach ($_POST['id_plato'] as $i => $id_plato) {
    $cantidad = $_POST['cantidad'][$i];
    $precio = $_POST['precio_unitario'][$i];

    if (!empty($id_plato)) {
      $sqlDetalle = "INSERT INTO detalle_venta (id_venta, id_plato, cantidad, precio_unitario)
                     VALUES ($id_venta, $id_plato, $cantidad, $precio)";
      $db->query($sqlDetalle);
    }
  }

  header("Location: ventas.php?agregado=1");
  exit;
}

// Actualizar venta
if (isset($_POST['editar'])) {
    $id = $_POST['id_venta'];
    $fecha = $_POST['fecha_venta'];
    $total = $_POST['total_venta'];
    $id_mesa = empty($_POST['id_mesa']) ? 'NULL' : "'".$_POST['id_mesa']."'";
    $id_usuario = $_POST['id_usuario'];
    $id_sucursal = $_POST['id_sucursal']; // 🔹 Nuevo campo

    $sql = "UPDATE ventas 
            SET fecha_venta='$fecha',
                total_venta='$total',
                id_mesa=$id_mesa,
                id_usuario='$id_usuario',
                id_sucursal='$id_sucursal'
            WHERE id_venta=$id";
    $db->query($sql);

    header("Location: ventas.php?editado=1");
    exit;
}

// Eliminar venta
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    // Primero eliminar los detalles asociados
    $db->query("DELETE FROM detalle_venta WHERE id_venta=$id");

    // Luego eliminar la venta
    $db->query("DELETE FROM ventas WHERE id_venta=$id");

    header("Location: ventas.php?eliminado=1");
    exit;
}

?>
