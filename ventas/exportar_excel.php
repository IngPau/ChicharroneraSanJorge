<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();
date_default_timezone_set('America/Guatemala');

// --- FILTROS ---
$filtro = "1=1";
if (!empty($_GET['fecha'])) {
    $fecha = $db->real_escape_string($_GET['fecha']);
    $filtro .= " AND v.fecha_venta = '$fecha'";
}
if (!empty($_GET['mes'])) {
    $mes = $db->real_escape_string($_GET['mes']);
    $filtro .= " AND MONTH(v.fecha_venta) = '$mes'";
}
if (!empty($_GET['sucursal'])) {
    $sucursal = $db->real_escape_string($_GET['sucursal']);
    $filtro .= " AND v.id_sucursal = '$sucursal'";
}

// --- Consulta ---
$ventas = $db->query("
  SELECT v.*, s.nombre_sucursal, u.nombre_usuario, c.nombre_cliente
  FROM ventas v
  INNER JOIN sucursales s ON v.id_sucursal = s.id_sucursal
  INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
  LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
  WHERE $filtro
  ORDER BY v.fecha_venta DESC
");

// --- Encabezado dinámico ---
$encabezado = "Reporte de Ventas";
if (!empty($_GET['fecha'])) {
    $encabezado .= " - Fecha: " . date("d/m/Y", strtotime($_GET['fecha']));
} elseif (!empty($_GET['mes'])) {
    $encabezado .= " - Mes: " . date("F", mktime(0,0,0,$_GET['mes'],1)) . " " . date("Y");
} else {
    $encabezado .= " - Todos los registros";
}

// --- Cabeceras para Excel ---
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_ventas.xls");

// --- Imprimir tabla ---
echo "<table border='1'>";
echo "<tr><th colspan='7'>$encabezado</th></tr>";
echo "<tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Total Q</th>
        <th>Mesa</th>
        <th>Usuario</th>
        <th>Sucursal</th>
        <th>Cliente</th>
      </tr>";

$totalGeneral = 0;
while ($v = $ventas->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$v['id_venta']}</td>";
    echo "<td>".date("d/m/Y", strtotime($v['fecha_venta']))."</td>";
    echo "<td>".number_format($v['total_venta'],2)."</td>";
    echo "<td>".($v['id_mesa'] ?: '-')."</td>";
    echo "<td>{$v['nombre_usuario']}</td>";
    echo "<td>{$v['nombre_sucursal']}</td>";
    echo "<td>".($v['nombre_cliente'] ?: '-')."</td>";
    echo "</tr>";
    $totalGeneral += $v['total_venta'];
}

// Total
echo "<tr><td colspan='2'><strong>Total General</strong></td><td colspan='5'>Q" . number_format($totalGeneral,2) . "</td></tr>";
echo "</table>";
?>


