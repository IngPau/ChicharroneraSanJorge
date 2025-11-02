<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();
date_default_timezone_set('America/Guatemala');

$hoy = date('Y-m-d');
$mesActual = date('m');
$anioActual = date('Y');

// ========== CONSULTAS PRINCIPALES ==========

// Ventas del día
$totalDia = $db->query("SELECT IFNULL(SUM(total_venta), 0) AS total FROM ventas WHERE fecha_venta = '$hoy'")
              ->fetch_assoc()['total'];

// Ventas del mes
$totalMes = $db->query("SELECT IFNULL(SUM(total_venta), 0) AS total FROM ventas WHERE MONTH(fecha_venta) = $mesActual AND YEAR(fecha_venta) = $anioActual")
              ->fetch_assoc()['total'];

// Empleados
$totalEmpleados = $db->query("SELECT COUNT(*) AS total FROM empleados")->fetch_assoc()['total'];

// Clientes
$totalClientes = $db->query("SELECT COUNT(*) AS total FROM clientes")->fetch_assoc()['total'];

// Sucursales
$totalSucursales = $db->query("SELECT COUNT(*) AS total FROM sucursales")->fetch_assoc()['total'];

// Vehículos asignados
$totalVehiculos = $db->query("SELECT COUNT(*) AS total FROM asignacion_vehiculo")->fetch_assoc()['total'];

// Pérdidas del mes
$totalPerdidas = $db->query("SELECT IFNULL(SUM(cantidad), 0) AS total FROM perdidas WHERE MONTH(fecha) = $mesActual AND YEAR(fecha) = $anioActual")
              ->fetch_assoc()['total'];

// Plato más vendido
$platoMasVendido = $db->query("
  SELECT p.nombre_plato, SUM(dv.cantidad) AS cantidad
  FROM detalle_venta dv
  JOIN platos p ON p.id_plato = dv.id_plato
  GROUP BY p.id_plato
  ORDER BY cantidad DESC
  LIMIT 1
")->fetch_assoc();

// Ventas por día (últimos 7)
$ventasSem = $db->query("
  SELECT fecha_venta, SUM(total_venta) AS total
  FROM ventas
  WHERE fecha_venta BETWEEN DATE_SUB('$hoy', INTERVAL 6 DAY) AND '$hoy'
  GROUP BY fecha_venta
  ORDER BY fecha_venta ASC
");

$labels = [];
$dataVentas = [];
while ($v = $ventasSem->fetch_assoc()) {
  $labels[] = $v['fecha_venta'];
  $dataVentas[] = (float)$v['total'];
}

// Top 5 platos
$topPlatos = $db->query("
  SELECT p.nombre_plato, SUM(dv.cantidad) AS cantidad
  FROM detalle_venta dv
  JOIN platos p ON p.id_plato = dv.id_plato
  GROUP BY p.id_plato
  ORDER BY cantidad DESC
  LIMIT 5
");

$topLabels = [];
$topCant = [];
while ($p = $topPlatos->fetch_assoc()) {
  $topLabels[] = $p['nombre_plato'];
  $topCant[] = (int)$p['cantidad'];
}

// Tabla top 10
$tablaTop = $db->query("
  SELECT p.nombre_plato, SUM(dv.cantidad) AS cantidad, SUM(dv.precio_unitario * dv.cantidad) AS ingreso
  FROM detalle_venta dv
  JOIN platos p ON p.id_plato = dv.id_plato
  GROUP BY p.id_plato
  ORDER BY cantidad DESC
  LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Inteligente | CSJ</title>
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../sidebar/sidebar.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body>
<div class="container">
  <?php include_once '../sidebar/sidebar.php'; ?>

  <main class="main">
<header class="header">
  <h1>Dashboard Inteligente</h1>
</header>


    <!-- TARJETAS -->
    <section class="cards">
      <div class="card"><div class="icon-wrap success"><i class='bx bx-dollar-circle'></i></div><div><h3>Ventas del Día</h3><p class="number">Q<?= number_format($totalDia, 2) ?></p></div></div>
      <div class="card"><div class="icon-wrap info"><i class='bx bx-wallet'></i></div><div><h3>Ventas del Mes</h3><p class="number">Q<?= number_format($totalMes, 2) ?></p></div></div>
      <div class="card"><div class="icon-wrap warning"><i class='bx bx-food-menu'></i></div><div><h3>Plato Más Vendido</h3><p><?= $platoMasVendido['nombre_plato'] ?? 'N/A' ?></p><span><?= $platoMasVendido['cantidad'] ?? 0 ?> unidades</span></div></div>
      <div class="card"><div class="icon-wrap"><i class='bx bx-group'></i></div><div><h3>Empleados</h3><p class="number"><?= $totalEmpleados ?></p></div></div>
      <div class="card"><div class="icon-wrap"><i class='bx bx-user-circle'></i></div><div><h3>Clientes</h3><p class="number"><?= $totalClientes ?></p></div></div>
      <div class="card"><div class="icon-wrap"><i class='bx bx-store'></i></div><div><h3>Sucursales</h3><p class="number"><?= $totalSucursales ?></p></div></div>
      <div class="card"><div class="icon-wrap"><i class='bx bx-car'></i></div><div><h3>Vehículos</h3><p class="number"><?= $totalVehiculos ?></p></div></div>
      <div class="card"><div class="icon-wrap danger"><i class='bx bx-down-arrow-alt'></i></div><div><h3>Pérdidas del Mes</h3><p class="number"><?= number_format($totalPerdidas, 2) ?> lb</p></div></div>
    </section>

    <!-- GRÁFICAS -->
    <section class="charts">
      <div class="chart-box">
        <h3>Ventas últimos 7 días</h3>
        <canvas id="chartVentas"></canvas>
      </div>
      <div class="chart-box">
        <h3>Top 5 Platos Más Vendidos</h3>
        <canvas id="chartTopPlatos"></canvas>
      </div>
    </section>

    <!-- TABLA -->
    <section class="tabla">
      <h3>Top 10 Platos Más Vendidos</h3>
      <table>
        <thead><tr><th>Plato</th><th>Cantidad</th><th>Ingreso</th></tr></thead>
        <tbody>
        <?php while($fila = $tablaTop->fetch_assoc()): ?>
          <tr>
            <td><?= $fila['nombre_plato'] ?></td>
            <td><?= $fila['cantidad'] ?></td>
            <td>Q<?= number_format($fila['ingreso'], 2) ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="dashboard.js"></script>

<!-- Pasar datos PHP al JS -->
<script>
  const ventasLabels = <?= json_encode($labels) ?>;
  const ventasData = <?= json_encode($dataVentas) ?>;
  const topLabels = <?= json_encode($topLabels) ?>;
  const topCant = <?= json_encode($topCant) ?>;
</script>

</body>
</html>
