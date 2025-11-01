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

// --- FILTRO por dirección (opcional) ---
$filtro = "1=1";
$direccion_encabezado = "Todos los registros";
if (!empty($_GET['direccion'])) {
    $direccion = $db->real_escape_string($_GET['direccion']);
    $filtro .= " AND direccion_proveedor LIKE '%$direccion%'";
    $direccion_encabezado = "Dirección: $direccion";
}

// --- Consulta de proveedores ---
$proveedores = $db->query("
    SELECT id_proveedor, nombre_proveedor, telefono_proveedor, correo_proveedor, direccion_proveedor
    FROM proveedores
    WHERE $filtro
    ORDER BY nombre_proveedor ASC
");

// --- Encabezado ---
$encabezado = "Reporte de Proveedores - $direccion_encabezado";

// --- Generar PDF horizontal ---
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,$encabezado,0,1,'C');
$pdf->Ln(5);

// --- Tabla ---
$pdf->SetFont('Arial','B',12);
$header = ['ID'=>20, 'Nombre'=>60, 'Teléfono'=>40, 'Correo'=>60, 'Dirección'=>70];
foreach($header as $col => $width){
    $pdf->Cell($width,10,$col,1,0,'C');
}
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial','',11);
if($proveedores && $proveedores->num_rows > 0){
    while($p = $proveedores->fetch_assoc()){
        // Calcular altura máxima de la fila
        $lineas = [
            ceil(strlen($p['nombre_proveedor'])/20),
            ceil(strlen($p['telefono_proveedor'])/15),
            ceil(strlen($p['correo_proveedor'])/25),
            ceil(strlen($p['direccion_proveedor'])/30)
        ];
        $maxLineas = max($lineas);
        $altoFila = 6 * $maxLineas;

        $pdf->Cell($header['ID'],$altoFila,$p['id_proveedor'],1);
        $pdf->Cell($header['Nombre'],$altoFila,utf8_decode($p['nombre_proveedor']),1);
        $pdf->Cell($header['Teléfono'],$altoFila,utf8_decode($p['telefono_proveedor']),1);
        $pdf->Cell($header['Correo'],$altoFila,utf8_decode($p['correo_proveedor']),1);
        $pdf->Cell($header['Dirección'],$altoFila,utf8_decode($p['direccion_proveedor']),1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(array_sum($header),10,'No se encontraron resultados.',1,1,'C');
}

// Descargar PDF
$pdf->Output('D','reporte_proveedores.pdf');
?>

