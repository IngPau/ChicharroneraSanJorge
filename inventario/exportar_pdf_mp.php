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
$almacen_nombre = "Todos los registros"; // Encabezado por defecto

if (!empty($_GET['almacen'])) {
    $almacen = $db->real_escape_string($_GET['almacen']);
    $filtro .= " AND i.id_almacen = '$almacen'";

    // Obtener nombre del almacén
    $res = $db->query("SELECT nombre FROM almacenes_sucursal WHERE id_almacen='$almacen'");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $almacen_nombre = $row['nombre'];
    }
}

// --- Consulta inventario ---
$inventario = $db->query("
    SELECT i.id_inventario, m.nombre_insumos, i.stock, i.cantidad_minima, m.unidad_medida, a.nombre AS nombre_almacen
    FROM inventario_materiaprima i
    INNER JOIN materiaprima m ON i.id_insumo = m.id_materia_prima
    INNER JOIN almacenes_sucursal a ON i.id_almacen = a.id_almacen
    WHERE $filtro
    ORDER BY m.nombre_insumos ASC
");

if(!$inventario){
    die("Error en la consulta: " . $db->error);
}

// --- Encabezado ---
$encabezado = "Inventario de Materia Prima - Almacén: $almacen_nombre";

// --- Generar PDF ---
$pdf = new FPDF('L','mm','A4'); // Horizontal
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,$encabezado,0,1,'C');
$pdf->Ln(5);

// Tabla
$pdf->SetFont('Arial','B',12);
$pdf->Cell(15,10,'ID',1);
$pdf->Cell(70,10,'Materia Prima',1);
$pdf->Cell(25,10,'Stock',1);
$pdf->Cell(35,10,'Cantidad Mínima',1);
$pdf->Cell(25,10,'Unidad',1);
$pdf->Cell(60,10,'Almacén',1);
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial','',11);
while($i = $inventario->fetch_assoc()){
    $pdf->Cell(15,10,$i['id_inventario'],1);
    $pdf->Cell(70,10,$i['nombre_insumos'],1);
    $pdf->Cell(25,10,$i['stock'],1);
    $pdf->Cell(35,10,$i['cantidad_minima'],1);
    $pdf->Cell(25,10,$i['unidad_medida'],1);
    $pdf->Cell(60,10,$i['nombre_almacen'],1);
    $pdf->Ln();
}

// Descargar PDF
$pdf->Output('D','inventario_materia_prima.pdf');
?>



