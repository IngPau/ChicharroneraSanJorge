<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

// Orden dinámico
$orden = (isset($_GET['orden']) && in_array($_GET['orden'], ['ASC','DESC'])) ? $_GET['orden'] : 'DESC';

// Catálogos
$proveedores = $db->query("SELECT id_proveedor, nombre_proveedor FROM proveedores ORDER BY nombre_proveedor");
$sucursales  = $db->query("SELECT id_sucursal, nombre_sucursal FROM sucursales ORDER BY nombre_sucursal");
$insumos     = $db->query("SELECT id_materia_prima, nombre_insumos, unidad_medida FROM materiaprima ORDER BY nombre_insumos");

// Compra para editar
$compraEditar = null;
$detalleEditar = null;
if (isset($_GET['editar'])) {
  $id = (int)$_GET['editar'];
  $res = $db->query("SELECT * FROM compra WHERE id_compra = $id");
  $compraEditar = $res ? $res->fetch_assoc() : null;

  // Traer primer detalle 
  $rDet = $db->query("SELECT id_insumo, cantidad_insumo, precio_unitario
                      FROM detalle_compra WHERE id_compra = $id LIMIT 1");
  $detalleEditar = $rDet ? $rDet->fetch_assoc() : null;
}

// Listado de compras
$compras = $db->query("
  SELECT c.id_compra, c.fecha_compra, c.total_compra, c.estado_compra,
         p.nombre_proveedor, s.nombre_sucursal
  FROM compra c
  LEFT JOIN proveedores p ON p.id_proveedor = c.id_proveedor
  LEFT JOIN sucursales  s ON s.id_sucursal  = c.id_sucursal
  ORDER BY c.id_compra $orden
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo Compras</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Estilos -->
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="compras.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Compras</h1>
      <h3>Gestión de Compras</h3>

      <!-- Flash message -->
      <?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="alert <?= $f['type']==='success'?'alert-success':'alert-danger' ?>">
          <?= htmlspecialchars($f['msg']) ?>
        </div>
      <?php endif; ?>

      <!-- Formulario -->
      <form method="POST" action="compras_crud.php" class="formulario" id="frmCompra">
        <input type="hidden" name="id_compra" value="<?= $compraEditar['id_compra'] ?? '' ?>">

        <label>Proveedor:</label>
        <select name="id_proveedor" id="id_proveedor" required>
          <option value="">Seleccione proveedor</option>
          <?php while ($p = $proveedores->fetch_assoc()):
            $sel = (isset($compraEditar['id_proveedor']) && (int)$compraEditar['id_proveedor']===(int)$p['id_proveedor']) ? 'selected' : '';
          ?>
            <option value="<?= $p['id_proveedor'] ?>" <?= $sel ?>>
              <?= htmlspecialchars($p['nombre_proveedor']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Sucursal:</label>
        <select name="id_sucursal" id="id_sucursal">
          <option value="">(Opcional)</option>
          <?php while ($s = $sucursales->fetch_assoc()):
            $sel = (isset($compraEditar['id_sucursal']) && (int)$compraEditar['id_sucursal']===(int)$s['id_sucursal']) ? 'selected' : '';
          ?>
            <option value="<?= $s['id_sucursal'] ?>" <?= $sel ?>>
              <?= htmlspecialchars($s['nombre_sucursal']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Fecha de compra:</label>
        <input type="date" name="fecha_compra" id="fecha_compra"
               value="<?= $compraEditar['fecha_compra'] ?? date('Y-m-d') ?>" required>

        <!-- ===== Detalle ===== -->
        <div style="height:8px"></div>
        <h4>Detalle de la compra</h4>

        <label>Insumo:</label>
        <select name="id_insumo" id="id_insumo" required>
          <option value="">Seleccione insumo</option>
          <?php while ($i = $insumos->fetch_assoc()):
            $sel = ($detalleEditar && (int)$detalleEditar['id_insumo']===(int)$i['id_materia_prima']) ? 'selected' : '';
          ?>
            <option value="<?= $i['id_materia_prima'] ?>" <?= $sel ?>>
              <?= htmlspecialchars($i['nombre_insumos']) ?><?= $i['unidad_medida'] ? ' ('.$i['unidad_medida'].')' : '' ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Cantidad:</label>
        <input type="number" step="0.001" min="0.001" name="cantidad_insumo" id="cantidad_insumo"
               value="<?= $detalleEditar['cantidad_insumo'] ?? '' ?>" required>

        <label>Precio unitario (Q):</label>
        <input type="number" step="0.01" min="0" name="precio_unitario" id="precio_unitario"
               value="<?= $detalleEditar['precio_unitario'] ?? '' ?>" required>

        <label>Total (Q):</label>
        <input type="number" step="0.01" min="0" name="total_compra" id="total_compra"
               value="<?= $compraEditar['total_compra'] ?? '' ?>" required readonly>

        <label>Estado:</label>
        <?php $est = $compraEditar['estado_compra'] ?? 'ACTIVA'; ?>
        <select name="estado_compra" id="estado_compra">
          <option value="ACTIVA"  <?= $est==='ACTIVA' ? 'selected' : '' ?>>ACTIVA</option>
          <option value="ANULADA" <?= $est==='ANULADA'? 'selected' : '' ?>>ANULADA</option>
        </select>

        <div class="botones">
          <?php if ($compraEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar">
              <i class="fas fa-save"></i> Actualizar
            </button>
            <a href="compras.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
          <?php else: ?>
            <button type="submit" name="agregar" class="btn btn-agregar">
              <i class="fas fa-plus"></i> Agregar
            </button>
          <?php endif; ?>
        </div>
      </form>

      <!-- Buscador y orden -->
      <div class="buscador">
        <input type="text" id="buscarCompra" placeholder="🔍 Buscar por proveedor, sucursal, fecha, estado">
        <label for="ordenCompras" style="margin-left:15px;font-weight:600;">Ordenar por ID:</label>
        <select id="ordenCompras">
          <option value="DESC" <?= ($orden=='DESC')?'selected':'' ?>>Más recientes primero</option>
          <option value="ASC"  <?= ($orden=='ASC') ?'selected':'' ?>>Más antiguas primero</option>
        </select>
      </div>

      <!-- Tabla -->
      <section class="tabla">
        <table id="tablaCompras">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Proveedor</th>
              <th>Sucursal</th>
              <th>Total (Q)</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($compras): while ($c = $compras->fetch_assoc()): ?>
            <tr>
              <td><?= $c['id_compra'] ?></td>
              <td><?= htmlspecialchars($c['fecha_compra']) ?></td>
              <td><?= htmlspecialchars($c['nombre_proveedor'] ?? '-') ?></td>
              <td><?= htmlspecialchars($c['nombre_sucursal']  ?? '-') ?></td>
              <td><?= number_format((float)$c['total_compra'], 2) ?></td>
              <td><?= htmlspecialchars($c['estado_compra']) ?></td>
              <td class="acciones">
                <a class="btn btn-editar" href="compras.php?editar=<?= $c['id_compra'] ?>" title="Editar">
                  <i class="fas fa-edit"></i>
                </a>
                <a class="btn btn-eliminar" href="compras_crud.php?eliminar=<?= $c['id_compra'] ?>" title="Eliminar"
                   onclick="return confirm('¿Eliminar compra #<?= $c['id_compra'] ?>?');">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
    // Cálculo automático del total = cantidad * precio
    const $ = (s)=>document.querySelector(s);
    const cant = $('#cantidad_insumo');
    const pu   = $('#precio_unitario');
    const tot  = $('#total_compra');

    function recalc() {
      const c = parseFloat(cant.value || '0');
      const p = parseFloat(pu.value || '0');
      const t = (c>0 && p>=0) ? (c*p) : 0;
      tot.value = t.toFixed(2);
    }
    cant?.addEventListener('input', recalc);
    pu  ?.addEventListener('input', recalc);
    // Recalcular al cargar (útil en modo editar)
    window.addEventListener('DOMContentLoaded', recalc);

    // Buscador en tabla
    document.addEventListener("DOMContentLoaded", () => {
      const inputBuscar = document.getElementById("buscarCompra");
      const filas = document.querySelectorAll("#tablaCompras tbody tr");
      const ordenSelect = document.getElementById("ordenCompras");

      inputBuscar.addEventListener("keyup", () => {
        const f = inputBuscar.value.toLowerCase();
        filas.forEach(tr=>{
          const fecha  = tr.cells[1].textContent.toLowerCase();
          const prov   = tr.cells[2].textContent.toLowerCase();
          const suc    = tr.cells[3].textContent.toLowerCase();
          const estado = tr.cells[5].textContent.toLowerCase();
          tr.style.display = (fecha.includes(f)||prov.includes(f)||suc.includes(f)||estado.includes(f)) ? "" : "none";
        });
      });

      ordenSelect.addEventListener("change", ()=>{
        const o = ordenSelect.value;
        window.location.href = `compras.php?orden=${o}`;
      });
    });
  </script>
</body>
</html>
