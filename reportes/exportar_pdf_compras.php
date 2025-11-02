<?php
// ---- NUNCA IMPRIMIR NADA ANTES DEL PDF ----

ob_start();

session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Redirige y termina sin enviar HTML
    header("Location: ../login/login.php");
    ob_end_clean();
    exit();
}

require_once '../conexion.php';
require_once '../libs/fpdf.php';

date_default_timezone_set('America/Guatemala');

$db = conectar();

// ------------------ Helpers ------------------
function toISO($s) {
    // Convierte UTF-8 a ISO-8859-1 para FPDF sin usar utf8_decode()
    $s = (string)$s;
    $out = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
    return $out !== false ? $out : $s;
}

function trim_for_pdf($s, $max, $suffix = '...') {
    // Recorta con multibyte en UTF-8 y luego convierte
    $s = (string)$s;
    if (mb_strlen($s, 'UTF-8') > $max) {
        $s = mb_substr($s, 0, $max, 'UTF-8') . $suffix;
    }
    return toISO($s);
}

// ------------------ Filtros ------------------
$filtro = "1=1";

$fecha_ini    = $_GET['fecha_ini']    ?? '';
$fecha_fin    = $_GET['fecha_fin']    ?? '';
$id_proveedor = isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : 0;
$id_sucursal  = isset($_GET['id_sucursal'])  ? (int)$_GET['id_sucursal']  : 0;
$estado       = $_GET['estado']       ?? '';
$texto        = $_GET['texto']        ?? '';

$encabezado_detalle = [];

// Rango de fechas
if ($fecha_ini !== '' && $fecha_fin !== '') {
    $fi = $db->real_escape_string($fecha_ini);
    $ff = $db->real_escape_string($fecha_fin);
    $filtro .= " AND c.fecha_compra BETWEEN '$fi' AND '$ff'";
    $encabezado_detalle[] = "Fechas: $fi a $ff";
} else {
    if ($fecha_ini !== '') {
        $fi = $db->real_escape_string($fecha_ini);
        $filtro .= " AND c.fecha_compra >= '$fi'";
        $encabezado_detalle[] = "Desde: $fi";
    }
    if ($fecha_fin !== '') {
        $ff = $db->real_escape_string($fecha_fin);
        $filtro .= " AND c.fecha_compra <= '$ff'";
        $encabezado_detalle[] = "Hasta: $ff";
    }
}

// Proveedor
if ($id_proveedor > 0) {
    $filtro .= " AND c.id_proveedor = $id_proveedor";
    $encabezado_detalle[] = "Proveedor ID: $id_proveedor";
}

// Sucursal
if ($id_sucursal > 0) {
    $filtro .= " AND c.id_sucursal = $id_sucursal";
    $encabezado_detalle[] = "Sucursal ID: $id_sucursal";
}

// Estado
if ($estado !== '') {
    $estado_esc = $db->real_escape_string($estado);
    $filtro .= " AND c.estado_compra = '$estado_esc'";
    $encabezado_detalle[] = "Estado: $estado_esc";
}

// proveedor o insumo
if ($texto !== '') {
    $t = $db->real_escape_string($texto);
    $filtro .= " AND ( p.nombre_proveedor LIKE '%$t%'
                    OR EXISTS (
                        SELECT 1
                        FROM detalle_compra dci
                        JOIN materiaprima mpi ON mpi.id_materia_prima = dci.id_insumo
                        WHERE dci.id_compra = c.id_compra
                          AND mpi.nombre_insumos LIKE '%$t%'
                    )
                  )";
    $encabezado_detalle[] = "Texto: $t";
}

if (!$encabezado_detalle) {
    $encabezado_detalle[] = "Todos los registros";
}

// ------------------ Consulta ------------------
$sql = "
  SELECT 
    c.id_compra,
    c.fecha_compra,
    c.estado_compra,
    c.total_compra,
    s.nombre_sucursal,
    p.nombre_proveedor,
    COALESCE(SUM(dc.cantidad_insumo * dc.precio_unitario),0) AS total_detalle,
    COUNT(DISTINCT dc.id_detalle_compra) AS items
  FROM compra c
  LEFT JOIN proveedores p     ON p.id_proveedor = c.id_proveedor
  LEFT JOIN sucursales  s     ON s.id_sucursal  = c.id_sucursal
  LEFT JOIN detalle_compra dc ON dc.id_compra   = c.id_compra
  WHERE $filtro
  GROUP BY c.id_compra, c.fecha_compra, c.estado_compra, c.total_compra, s.nombre_sucursal, p.nombre_proveedor
  ORDER BY c.id_compra DESC
";
$compras = $db->query($sql);

// ------------------ Totales ------------------
$tot_reg     = 0;
$tot_compra  = 0.0;
$tot_detalle = 0.0;
$tot_items   = 0;

// ------------------ PDF ------------------
$encabezado = "Reporte de Compras - " . implode(" | ", $encabezado_detalle);

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, toISO($encabezado), 0, 1, 'C');

// Subtítulo: fecha/hora
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, toISO('Generado: ' . date('Y-m-d H:i')), 0, 1, 'C');
$pdf->Ln(3);

// Encabezados tabla
$pdf->SetFont('Arial', 'B', 11);
$cols = [
    'ID'             => 15,
    'Fecha'          => 25,
    'Sucursal'       => 45,
    'Proveedor'      => 78,
    'Estado'         => 25,
    'Total compra'   => 32,
    'Total detalle'  => 32,
    'Ítems'          => 18,
];

foreach ($cols as $col => $w) {
    $pdf->Cell($w, 9, toISO($col), 1, 0, 'C');
}
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial', '', 10);

if ($compras && $compras->num_rows > 0) {
    while ($c = $compras->fetch_assoc()) {
        $tot_reg++;
        $tot_compra  += (float)$c['total_compra'];
        $tot_detalle += (float)$c['total_detalle'];
        $tot_items   += (int)$c['items'];

        $id        = (int)$c['id_compra'];
        $fecha     = (string)$c['fecha_compra'];
        $sucursal  = $c['nombre_sucursal']  ?? '-';
        $proveedor = $c['nombre_proveedor'] ?? '-';
        $estadoVal = $c['estado_compra']    ?? '-';
        $tCompra   = number_format((float)$c['total_compra'], 2);
        $tDetalle  = number_format((float)$c['total_detalle'], 2);
        $items     = (int)$c['items'];

        $h = 8;

        $pdf->Cell($cols['ID'],            $h, $id,                 1, 0, 'R');
        $pdf->Cell($cols['Fecha'],         $h, toISO($fecha),       1, 0, 'L');
        $pdf->Cell($cols['Sucursal'],      $h, trim_for_pdf($sucursal, 30), 1, 0, 'L');
        $pdf->Cell($cols['Proveedor'],     $h, trim_for_pdf($proveedor, 50),1, 0, 'L');
        $pdf->Cell($cols['Estado'],        $h, toISO($estadoVal),   1, 0, 'C');
        $pdf->Cell($cols['Total compra'],  $h, toISO('Q '.$tCompra),1, 0, 'R');
        $pdf->Cell($cols['Total detalle'], $h, toISO('Q '.$tDetalle),1,0, 'R');
        $pdf->Cell($cols['Ítems'],         $h, $items,              1, 0, 'C');
        $pdf->Ln();
    }

    // Totales
    $pdf->SetFont('Arial', 'B', 11);
    $anchoTotales = $cols['ID'] + $cols['Fecha'] + $cols['Sucursal'] + $cols['Proveedor'] + $cols['Estado'];
    $pdf->Cell($anchoTotales,          9, toISO('Totales:'),             1, 0, 'R');
    $pdf->Cell($cols['Total compra'],  9, toISO('Q ' . number_format($tot_compra, 2)),   1, 0, 'R');
    $pdf->Cell($cols['Total detalle'], 9, toISO('Q ' . number_format($tot_detalle, 2)), 1, 0, 'R');
    $pdf->Cell($cols['Ítems'],         9, $tot_items,                    1, 1, 'C');

} else {
    $pdf->Cell(array_sum($cols), 10, toISO('No se encontraron resultados.'), 1, 1, 'C');
}

// ---- LIMPIA CUALQUIER OUTPUT ANTES DE ENVIAR EL PDF ----
$len = ob_get_length();
if ($len !== false && $len > 0) {
    ob_end_clean(); // limpia buffer para que Output no falle
} else {
    // Si no hay buffer, asegúrate de no dejarlo abierto
    @ob_end_clean();
}

// Enviar PDF (descarga)
$pdf->Output('D', 'reporte_compras.pdf');
exit;
