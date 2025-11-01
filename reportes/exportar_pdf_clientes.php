<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
require_once '../libs/fpdf.php';
$db = conectar();
date_default_timezone_set('America/Guatemala');

// --- FILTRO por dirección ---
$filtro = "1=1";
$direccion_filtro = "";
if (!empty($_GET['direccion'])) {
    $direccion_filtro = $db->real_escape_string($_GET['direccion']);
    $filtro .= " AND direccion_cliente LIKE '%$direccion_filtro%'";
}

// --- Consulta de clientes ---
$clientes = $db->query("
    SELECT id_cliente, nombre_cliente, apellido_cliente, telefono_cliente, correo_cliente, direccion_cliente
    FROM clientes
    WHERE $filtro
    ORDER BY nombre_cliente ASC
");

if(!$clientes){
    die("Error en la consulta: " . $db->error);
}

// --- Encabezado dinámico ---
$encabezado = "Listado de Clientes";
if (!empty($direccion_filtro)) {
    $encabezado .= " - Dirección: $direccion_filtro";
} else {
    $encabezado .= " - Todos los registros";
}

// --- Generar PDF en horizontal ---
$pdf = new FPDF('L','mm','A4'); // 'L' = landscape
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,utf8_decode($encabezado),0,1,'C');
$pdf->Ln(5);

// Tabla
$pdf->SetFont('Arial','B',12);
// Ajusta los anchos según el contenido
$pdf->Cell(15,10,'ID',1);
$pdf->Cell(50,10,'Nombre Completo',1);
$pdf->Cell(35,10,'Teléfono',1);
$pdf->Cell(55,10,'Correo',1);
$pdf->Cell(80,10,'Dirección',1);
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial','',11);
while($c = $clientes->fetch_assoc()){
    $pdf->Cell(15,10,$c['id_cliente'],1);
    $pdf->Cell(50,10,utf8_decode($c['nombre_cliente'].' '.$c['apellido_cliente']),1);
    $pdf->Cell(35,10,utf8_decode($c['telefono_cliente']),1);
    $pdf->Cell(55,10,utf8_decode($c['correo_cliente']),1);
    $pdf->Cell(80,10,utf8_decode($c['direccion_cliente']),1);
    $pdf->Ln();
}

// Descargar PDF
$pdf->Output('D','listado_clientes.pdf');
?>


