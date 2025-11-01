<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
require_once '../libs/fpdf.php'; // Asegúrate de que la ruta sea correcta
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

// --- Generar PDF ---
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10, $encabezado, 0, 1, 'C');
$pdf->Ln(5);

// Tabla
$pdf->SetFont('Arial','B',12);
$pdf->Cell(15,10,'ID',1);
$pdf->Cell(25,10,'Fecha',1);
$pdf->Cell(25,10,'Total Q',1);
$pdf->Cell(20,10,'Mesa',1);
$pdf->Cell(40,10,'Usuario',1);
$pdf->Cell(30,10,'Sucursal',1);
$pdf->Cell(35,10,'Cliente',1);
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial','',11);
$totalGeneral = 0;

while ($v = $ventas->fetch_assoc()) {
    $pdf->Cell(15,10,$v['id_venta'],1);
    $pdf->Cell(25,10,date("d/m/Y", strtotime($v['fecha_venta'])),1);
    $pdf->Cell(25,10,number_format($v['total_venta'],2),1);
    $pdf->Cell(20,10,$v['id_mesa'] ?: '-',1);
    $pdf->Cell(40,10,$v['nombre_usuario'],1);
    $pdf->Cell(30,10,$v['nombre_sucursal'],1);
    $pdf->Cell(35,10,$v['nombre_cliente'] ?: '-',1);
    $pdf->Ln();
    $totalGeneral += $v['total_venta'];
}

// Total
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10, "Total General: Q" . number_format($totalGeneral,2),0,1,'R');

// Descargar PDF
$pdf->Output('D','reporte_ventas.pdf');
?>
