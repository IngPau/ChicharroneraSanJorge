<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
require_once '../libs/fpdf.php';
$db = conectar();

// --- FILTRO por almacén ---
$filtro = "1=1";
$almacen_nombre = "Todos los registros";

if (!empty($_GET['almacen'])) {
    $almacen = $db->real_escape_string($_GET['almacen']);
    $filtro .= " AND i.id_almacen = '$almacen'";

    // Obtener nombre del almacén
    $res = $db->query("SELECT nombre FROM almacenes_sucursal WHERE id_almacen='$almacen'");
    if($res && $res->num_rows>0){
        $row = $res->fetch_assoc();
        $almacen_nombre = $row['nombre'];
    }
}

// --- Consulta inventario ---
$inventario = $db->query("
    SELECT i.id_inventario_mobiliario, m.nombre_mobiliario, i.stock, a.nombre AS nombre_almacen
    FROM inventario_mobiliario i
    INNER JOIN mobiliario m ON i.id_mobiliario = m.id_mobiliario
    INNER JOIN almacenes_sucursal a ON i.id_almacen = a.id_almacen
    WHERE $filtro
    ORDER BY m.nombre_mobiliario ASC
");

if(!$inventario){
    die("Error en la consulta: " . $db->error);
}

// --- Encabezado ---
$encabezado = "Inventario de Mobiliario - Almacén: $almacen_nombre";

// --- Generar PDF ---
$pdf = new FPDF('L','mm','A4'); // Horizontal
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,$encabezado,0,1,'C');
$pdf->Ln(5);

// Tabla
$pdf->SetFont('Arial','B',12);
$pdf->Cell(15,10,'ID',1);
$pdf->Cell(100,10,'Mobiliario',1);
$pdf->Cell(30,10,'Stock',1);
$pdf->Cell(60,10,'Almacén',1);
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial','',11);
while($i = $inventario->fetch_assoc()){
    $pdf->Cell(15,10,$i['id_inventario_mobiliario'],1);
    $pdf->Cell(100,10,$i['nombre_mobiliario'],1);
    $pdf->Cell(30,10,$i['stock'],1);
    $pdf->Cell(60,10,$i['nombre_almacen'],1);
    $pdf->Ln();
}

// Descargar PDF
$pdf->Output('D','inventario_mobiliario.pdf');

