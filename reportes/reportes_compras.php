<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

// =========================
// Filtros (GET)
// =========================
$fecha_ini = $_GET['fecha_ini'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_proveedor = isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : 0;
$id_sucursal  = isset($_GET['id_sucursal'])  ? (int)$_GET['id_sucursal']  : 0;
$estado       = $_GET['estado'] ?? ''; // ACTIVA, ANULADA, etc si lo manejas
$texto        = $_GET['texto']  ?? ''; // buscar por proveedor/insumo

$cond = "1=1";
if ($fecha_ini !== '' && $fecha_fin !== '') {
  $fi = $db->real_escape_string($fecha_ini);
  $ff = $db->real_escape_string($fecha_fin);
  $cond .= " AND c.fecha_compra BETWEEN '$fi' AND '$ff'";
} else {
  if ($fecha_ini !== '') {
    $fi = $db->real_escape_string($fecha_ini);
    $cond .= " AND c.fecha_compra >= '$fi'";
  }
  if ($fecha_fin !== '') {
    $ff = $db->real_escape_string($fecha_fin);
    $cond .= " AND c.fecha_compra <= '$ff'";
  }
}

if ($id_proveedor > 0) {
  $cond .= " AND c.id_proveedor = $id_proveedor";
}
if ($id_sucursal > 0) {
  $cond .= " AND c.id_sucursal = $id_sucursal";
}
if ($estado !== '') {
  $estado_esc = $db->real_escape_string($estado);
  $cond .= " AND c.estado_compra = '$estado_esc'";
}
if ($texto !== '') {
  $t = $db->real_escape_string($texto);
  // busca por proveedor o por nombre de insumo de los detalles
  $cond .= " AND (
      p.nombre_proveedor LIKE '%$t%' OR
      EXISTS (
        SELECT 1
        FROM detalle_compra dc
        JOIN materiaprima mp ON mp.id_materia_prima = dc.id_insumo
        WHERE dc.id_compra = c.id_compra
          AND mp.nombre_insumos LIKE '%$t%'
      )
    )";
}

// =========================
// Catálogos para filtros
// =========================
$proveedores = $db->query("SELECT id_proveedor, nombre_proveedor FROM proveedores ORDER BY nombre_proveedor");
$sucursales  = $db->query("SELECT id_sucursal,  nombre_sucursal  FROM sucursales  ORDER BY nombre_sucursal");

// =========================
// Consulta principal (resumen por compra)
// =========================
// total_compra viene en compra; por si no lo mantienes por trigger,
// calculo total_detalle = SUM(cantidad * precio) y lo muestro también.
$q = "
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
$resCompras = $db->query($q);

// Totales generales (pie de tabla)
$totales = ['monto'=>0.0, 'monto_calc'=>0.0, 'items'=>0, 'registros'=>0];
if ($resCompras) {
  // no iteramos aún; lo haremos al pintar la tabla
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Compras</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../ventas/ventas.css"><!-- reutilizamos estilos de tablas/botones -->
  <link rel="icon" type="image/png" href="/DW/Compras/img/logo.png?v=3">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous"/>
  <style>
    .filtros { display:flex; flex-wrap:wrap; gap:18px; align-items:flex-end; }
    .filtros .field { display:flex; flex-direction:column; min-width:180px; }
    .kpi-mini { display:flex; gap:12px; margin:16px 0; }
    .kpi-mini .card { background:#fff; border:1px solid #e9ecef; border-radius:10px; padding:10px 14px; }
    .kpi-mini .title { font-size:12px; color:#6c757d; text-transform:uppercase; letter-spacing:.3px; }
    .kpi-mini .value { font-size:18px; font-weight:800; }
    .tabla-ventas table td small { color:#6c757d; }
  </style>
</head>
<body>
<div class="container">
  <?php include '../SideBar/sidebar.php'; ?>
  <main class="main">
    <h1>Reporte de Compras</h1>

    <!-- Filtros -->
    <form method="GET" class="formulario">
      <div class="filtros">
        <div class="field">
          <label>Fecha inicial:</label>
          <input type="date" name="fecha_ini" value="<?= htmlspecialchars($fecha_ini) ?>">
        </div>
        <div class="field">
          <label>Fecha final:</label>
          <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
        </div>
        <div class="field">
          <label>Proveedor:</label>
          <select name="id_proveedor">
            <option value="0">Todos</option>
            <?php while($p = $proveedores->fetch_assoc()): ?>
              <option value="<?= $p['id_proveedor'] ?>" <?= ($id_proveedor == (int)$p['id_proveedor'])?'selected':'' ?>>
                <?= htmlspecialchars($p['nombre_proveedor']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="field">
          <label>Sucursal:</label>
          <select name="id_sucursal">
            <option value="0">Todas</option>
            <?php while($s = $sucursales->fetch_assoc()): ?>
              <option value="<?= $s['id_sucursal'] ?>" <?= ($id_sucursal == (int)$s['id_sucursal'])?'selected':'' ?>>
                <?= htmlspecialchars($s['nombre_sucursal']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="field">
          <label>Estado:</label>
          <select name="estado">
            <option value="">Todos</option>
            <option value="ACTIVA"   <?= ($estado==='ACTIVA'  ?'selected':'') ?>>ACTIVA</option>
            <option value="ANULADA"  <?= ($estado==='ANULADA' ?'selected':'') ?>>ANULADA</option>
          </select>
        </div>
        <div class="field" style="min-width:250px;">
          <label>Buscar (proveedor/insumo):</label>
          <input type="text" name="texto" value="<?= htmlspecialchars($texto) ?>" placeholder="Ej. Lomo / Carnes del Norte">
        </div>

        <div class="field" style="gap:8px;">
          <button type="submit" class="btn btn-agregar"><i class="fas fa-search"></i> Filtrar</button>
          <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn btn-cancelar"><i class="fas fa-rotate-left"></i> Limpiar</a>
        </div>
      </div>
    </form>

    <!-- Acciones -->
    <div style="margin: 14px 0; display:flex; gap:10px;">
      <a class="btn btn-agregar" target="_blank"
         href="exportar_pdf_compras.php?<?= http_build_query($_GET) ?>">
        <i class="fas fa-file-pdf"></i> Exportar PDF
      </a>
      <a class="btn btn-agregar"
         href="exportar_excel_compras.php?<?= http_build_query($_GET) ?>">
        <i class="fas fa-file-excel"></i> Exportar Excel
      </a>
      <button type="button" id="btnPrint" class="btn btn-agregar">
        <i class="fas fa-print"></i> Imprimir
      </button>
    </div>

    <!-- KPIs mini -->
    <div class="kpi-mini">
      <?php
      // Reejecuta la consulta para sumar totales si hace falta (o calcula al vuelo en el loop)
      ?>
      <div class="card">
        <div class="title">Registros</div>
        <div class="value" id="kpiReg">0</div>
      </div>
      <div class="card">
        <div class="title">Total (columna compra)</div>
        <div class="value" id="kpiMonto">Q 0.00</div>
      </div>
      <div class="card">
        <div class="title">Total (calculado por detalle)</div>
        <div class="value" id="kpiMontoCalc">Q 0.00</div>
      </div>
      <div class="card">
        <div class="title">Ítems</div>
        <div class="value" id="kpiItems">0</div>
      </div>
    </div>

    <!-- Tabla -->
    <section class="tabla-ventas">
      <table>
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
        <tbody>
          <?php if ($resCompras && $resCompras->num_rows > 0): ?>
            <?php while($r = $resCompras->fetch_assoc()): 
              $totales['registros']++;
              $totales['monto']      += (float)$r['total_compra'];
              $totales['monto_calc'] += (float)$r['total_detalle'];
              $totales['items']      += (int)$r['items'];
            ?>
              <tr>
                <td><?= (int)$r['id_compra'] ?></td>
                <td><?= htmlspecialchars($r['fecha_compra']) ?></td>
                <td><?= htmlspecialchars($r['nombre_sucursal'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['nombre_proveedor'] ?? '-') ?></td>
                <td>
                  <?php if ($r['estado_compra']==='ANULADA'): ?>
                    <span class="badge" style="background:#dc3545;color:#fff;padding:2px 6px;border-radius:6px;">ANULADA</span>
                  <?php else: ?>
                    <span class="badge" style="background:#198754;color:#fff;padding:2px 6px;border-radius:6px;"><?= htmlspecialchars($r['estado_compra']) ?></span>
                  <?php endif; ?>
                </td>
                <td style="text-align:right;">Q <?= number_format((float)$r['total_compra'], 2) ?></td>
                <td style="text-align:right;">
                  Q <?= number_format((float)$r['total_detalle'], 2) ?>
                  <?php if (abs((float)$r['total_compra'] - (float)$r['total_detalle']) > 0.009): ?>
                    <br><small>↺ dif: Q <?= number_format((float)$r['total_detalle'] - (float)$r['total_compra'], 2) ?></small>
                  <?php endif; ?>
                </td>
                <td style="text-align:center;"><?= (int)$r['items'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-center text-muted">No hay compras con los filtros aplicados.</td></tr>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="5" style="text-align:right;">Totales:</th>
            <th style="text-align:right;">Q <?= number_format($totales['monto'], 2) ?></th>
            <th style="text-align:right;">Q <?= number_format($totales['monto_calc'], 2) ?></th>
            <th style="text-align:center;"><?= (int)$totales['items'] ?></th>
          </tr>
        </tfoot>
      </table>
    </section>
  </main>
</div>

<script>
  // Set KPI mini
  document.getElementById('kpiReg').textContent      = '<?= (int)$totales['registros'] ?>';
  document.getElementById('kpiMonto').textContent     = 'Q <?= number_format($totales['monto'], 2) ?>';
  document.getElementById('kpiMontoCalc').textContent = 'Q <?= number_format($totales['monto_calc'], 2) ?>';
  document.getElementById('kpiItems').textContent     = '<?= (int)$totales['items'] ?>';

  // Imprimir
  document.getElementById('btnPrint').addEventListener('click', () => {
    const w = window.open('', '_blank');
    w.document.write('<html><head><title>Reporte de Compras</title>');
    w.document.write('<link rel="stylesheet" href="../globales.css">');
    w.document.write('<style>table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:6px}</style>');
    w.document.write('</head><body>');
    let enc = '<h2>Reporte de Compras</h2>';
    <?php if ($fecha_ini || $fecha_fin || $id_proveedor || $id_sucursal || $estado || $texto): ?>
      enc += '<p><strong>Filtros:</strong> '
        + '<?= $fecha_ini ? "Desde: ".htmlspecialchars($fecha_ini)." " : "" ?>'
        + '<?= $fecha_fin ? "Hasta: ".htmlspecialchars($fecha_fin)." " : "" ?>'
        + '<?= $id_proveedor ? "Proveedor ID: ".(int)$id_proveedor." " : "" ?>'
        + '<?= $id_sucursal ? "Sucursal ID: ".(int)$id_sucursal." " : "" ?>'
        + '<?= $estado ? "Estado: ".htmlspecialchars($estado)." " : "" ?>'
        + '<?= $texto ? "Texto: ".htmlspecialchars($texto)." " : "" ?>'
        + '</p>';
    <?php endif; ?>
    w.document.write(enc);
    w.document.write(document.querySelector(".tabla-ventas").outerHTML);
    w.document.write('</body></html>');
    w.document.close();
    w.print();
  });
</script>
</body>
</html>
