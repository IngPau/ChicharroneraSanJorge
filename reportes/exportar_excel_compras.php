<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php"); exit();
}

require_once '../conexion.php';
$db = conectar();

/* ========== Filtros */
$fecha_ini   = $_GET['fecha_ini'] ?? '';
$fecha_fin   = $_GET['fecha_fin'] ?? '';
$id_proveedor= isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : 0;
$id_sucursal = isset($_GET['id_sucursal'])  ? (int)$_GET['id_sucursal']  : 0;
$estado      = $_GET['estado'] ?? '';
$texto       = $_GET['texto']  ?? '';

$cond = "1=1";
if ($fecha_ini !== '' && $fecha_fin !== '') {
  $fi = $db->real_escape_string($fecha_ini);
  $ff = $db->real_escape_string($fecha_fin);
  $cond .= " AND c.fecha_compra BETWEEN '$fi' AND '$ff'";
} else {
  if ($fecha_ini !== '') { $fi = $db->real_escape_string($fecha_ini); $cond .= " AND c.fecha_compra >= '$fi'"; }
  if ($fecha_fin !== '') { $ff = $db->real_escape_string($fecha_fin); $cond .= " AND c.fecha_compra <= '$ff'"; }
}
if ($id_proveedor > 0) { $cond .= " AND c.id_proveedor = $id_proveedor"; }
if ($id_sucursal  > 0) { $cond .= " AND c.id_sucursal  = $id_sucursal"; }
if ($estado !== '')    { $estado_esc = $db->real_escape_string($estado); $cond .= " AND c.estado_compra = '$estado_esc'"; }
if ($texto !== '') {
  $t = $db->real_escape_string($texto);
  $cond .= " AND ( p.nombre_proveedor LIKE '%$t%'
                OR EXISTS (
                     SELECT 1
                     FROM detalle_compra dc
                     JOIN materiaprima mp ON mp.id_materia_prima = dc.id_insumo
                     WHERE dc.id_compra = c.id_compra
                       AND mp.nombre_insumos LIKE '%$t%'
                   )
               )";
}

/* ==========================    Consulta datos del reporte ========================== */
$sql = "
  SELECT 
    c.id_compra,
    c.fecha_compra,
    c.estado_compra,
    c.total_compra,
    s.nombre_sucursal,
    p.nombre_proveedor,
    COUNT(DISTINCT dc.id_detalle_compra) AS items,
    COALESCE(SUM(dc.cantidad_insumo * dc.precio_unitario), 0) AS total_detalle
  FROM compra c
  LEFT JOIN proveedores p  ON p.id_proveedor = c.id_proveedor
  LEFT JOIN sucursales  s  ON s.id_sucursal  = c.id_sucursal
  LEFT JOIN detalle_compra dc ON dc.id_compra = c.id_compra
  WHERE $cond
  GROUP BY c.id_compra, c.fecha_compra, c.estado_compra, c.total_compra, s.nombre_sucursal, p.nombre_proveedor
  ORDER BY c.id_compra DESC
";
$res = $db->query($sql);

// totales
$totReg=0; $totMonto=0.0; $totCalc=0.0; $totItems=0;

$filename = 'reporte_compras_'.date('Ymd_His').'.xls';
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// estilos mínimos para Excel HTML
echo '<html><head><meta charset="utf-8"><style>
table{border-collapse:collapse;}
th,td{border:1px solid #999;padding:5px;}
th{background:#f0f0f0}
.badge-ok{background:#198754;color:#fff;padding:2px 6px;border-radius:6px;}
.badge-no{background:#dc3545;color:#fff;padding:2px 6px;border-radius:6px;}
</style></head><body>';

echo '<h3>Reporte de Compras</h3>';
$fx = [];
if ($fecha_ini)   $fx[] = 'Desde: '.htmlspecialchars($fecha_ini);
if ($fecha_fin)   $fx[] = 'Hasta: '.htmlspecialchars($fecha_fin);
if ($id_proveedor)$fx[] = 'Proveedor ID: '.(int)$id_proveedor;
if ($id_sucursal) $fx[] = 'Sucursal ID: '.(int)$id_sucursal;
if ($estado)      $fx[] = 'Estado: '.htmlspecialchars($estado);
if ($texto)       $fx[] = 'Texto: '.htmlspecialchars($texto);
if ($fx) echo '<p><b>Filtros:</b> '.implode(' | ',$fx).'</p>';

echo '<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Sucursal</th>
      <th>Proveedor</th>
      <th>Estado</th>
      <th style="text-align:right;">Total (compra)</th>
      <th style="text-align:right;">Total (detalle)</th>
      <th style="text-align:center;">Ítems</th>
    </tr>
  </thead>
  <tbody>';

if ($res && $res->num_rows) {
  while($r = $res->fetch_assoc()){
    $totReg++;
    $totMonto += (float)$r['total_compra'];
    $totCalc  += (float)$r['total_detalle'];
    $totItems += (int)$r['items'];
    $badge = ($r['estado_compra']==='ANULADA')
      ? '<span class="badge-no">ANULADA</span>'
      : '<span class="badge-ok">'.htmlspecialchars($r['estado_compra']).'</span>';
    $dif = abs((float)$r['total_compra'] - (float)$r['total_detalle']) > 0.009
         ? '<div style="font-size:10px;color:#666;">↺ dif: Q '.number_format($r['total_detalle'] - $r['total_compra'],2).'</div>'
         : '';
    echo '<tr>
      <td>'.(int)$r['id_compra'].'</td>
      <td>'.htmlspecialchars($r['fecha_compra']).'</td>
      <td>'.htmlspecialchars($r['nombre_sucursal'] ?? '-').'</td>
      <td>'.htmlspecialchars($r['nombre_proveedor'] ?? '-').'</td>
      <td>'.$badge.'</td>
      <td style="text-align:right;">Q '.number_format((float)$r['total_compra'],2).'</td>
      <td style="text-align:right;">Q '.number_format((float)$r['total_detalle'],2).$dif.'</td>
      <td style="text-align:center;">'.(int)$r['items'].'</td>
    </tr>';
  }
} else {
  echo '<tr><td colspan="8" style="text-align:center;color:#888;">No hay registros</td></tr>';
}
echo '</tbody>
  <tfoot>
    <tr>
      <th colspan="5" style="text-align:right;">Totales:</th>
      <th style="text-align:right;">Q '.number_format($totMonto,2).'</th>
      <th style="text-align:right;">Q '.number_format($totCalc,2).'</th>
      <th style="text-align:center;">'.$totItems.'</th>
    </tr>
  </tfoot>
</table>';

echo '<p>Registros: '.$totReg.' | Generado: '.date('Y-m-d H:i').'</p>';
echo '</body></html>';
