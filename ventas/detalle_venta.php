<?php
require_once '../conexion.php';
$db = conectar();

date_default_timezone_set('America/Guatemala');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
  die("ID de venta inválido.");
}

$venta = $db->query("SELECT * FROM ventas WHERE id_venta = $id")->fetch_assoc();

if (!$venta) {
  die("Venta no encontrada.");
}

$detalles = $db->query("
  SELECT dv.*, p.nombre_plato
  FROM detalle_venta dv
  JOIN platos p ON dv.id_plato = p.id_plato
  WHERE dv.id_venta = $id
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle de Venta #<?= $venta['id_venta'] ?></title>
  <link rel="stylesheet" href="detalle.css">
</head>
<body>
  <div class="detalle-container">

    <header class="detalle-header">
      <div class="titulo">
        <h1>Chicharronería San Jorge</h1>
        <p>Detalle de la Venta</p>
      </div>
      <div class="info">
        <p><strong>ID:</strong> <?= $venta['id_venta'] ?></p>
        <p><strong>Fecha:</strong> <?= date("d/m/Y - H:i", strtotime($venta['fecha_venta'])) ?></p>
        <p><strong>Total:</strong> Q<?= number_format($venta['total_venta'], 2) ?></p>
      </div>
    </header>

    <section class="detalle-tabla">
      <h2>Platos Vendidos</h2>
      <table>
        <thead>
          <tr>
            <th>Plato</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($fila = $detalles->fetch_assoc()): ?>
            <tr>
              <td data-label="Plato"><?= $fila['nombre_plato'] ?></td>
              <td data-label="Cantidad"><?= $fila['cantidad'] ?></td>
              <td data-label="Precio Unitario">Q<?= number_format($fila['precio_unitario'], 2) ?></td>
              <td data-label="Subtotal">Q<?= number_format($fila['cantidad'] * $fila['precio_unitario'], 2) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

    <footer class="detalle-footer">
      <button class="btn-volver" onclick="history.back()">← Volver</button>
      <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir</button>
    </footer>

  </div>

</body>
</html>
