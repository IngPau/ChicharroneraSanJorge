<?php
require_once '../conexion.php';
$db = conectar();

// Obtener ventas
$ventas = $db->query("SELECT * FROM ventas ORDER BY id_venta DESC");

// Venta a editar
$ventaEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM ventas WHERE id_venta=$id");
  $ventaEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Ventas</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../ventas/ventas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Ventas</h1>
      <h3>Gestión de Ventas</h3>

      <!-- Formulario -->
      <form method="POST" action="ventas_crud.php" class="formulario">
        <input type="hidden" name="id_venta" value="<?= $ventaEditar['id_venta'] ?? '' ?>">
        <label>Fecha:</label>
        <input type="date" name="fecha_venta" value="<?= $ventaEditar['fecha_venta'] ?? '' ?>" required>
        <label>Total (Q):</label>
        <input type="number" name="total_venta" id="total_venta" readonly class="bloqueado">
        <label>ID Mesa (opcional):</label>
        <select name="id_mesa">
          <option value="">-- Sin mesa asignada --</option>
          <?php
          $mesas = $db->query("SELECT id_mesas FROM mesas");
          while ($m = $mesas->fetch_assoc()) {
            $selected = ($ventaEditar['id_mesa'] ?? '') == $m['id_mesas'] ? 'selected' : '';
            echo "<option value='{$m['id_mesas']}' $selected>Mesa {$m['id_mesas']}</option>";
          }
          ?>
        </select>
        <label>ID Usuario:</label>
        <input type="number" name="id_usuario" value="<?= $ventaEditar['id_usuario'] ?? '' ?>" required>
        <div class="bloque-detalle">
          <h3>Detalle de Venta</h3>
          <div id="detalle-container">
            <div class="detalle-item">
              <label>Plato:</label>
              <select name="id_plato[]" class="select-plato" required>
                <option value="">-- Selecciona un plato --</option>
                <?php
                $platos = $db->query("SELECT id_plato, nombre_plato FROM platos");
                while ($p = $platos->fetch_assoc()) {
                  echo "<option value='{$p['id_plato']}'>{$p['nombre_plato']}</option>";
                }
                ?>
              </select>

              <label>Cantidad:</label>
              <input type="number" name="cantidad[]" class="cantidad" min="1" required>

              <label>Precio Unitario:</label>
              <input type="number" name="precio_unitario[]" class="precio-unitario" required readonly class="bloqueado">

              <label>Subtotal:</label>
              <input type="number" class="subtotal" readonly class="bloqueado">
            </div>
          </div>

          <button type="button" class="agregar-detalle" onclick="agregarDetalle()">➕ Agregar otro plato</button>
          <div class="botones">
            <?php if ($ventaEditar): ?>
              <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
              <a href="ventas.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
            <?php else: ?>
              <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
            <?php endif; ?>
          </div>
        </div>

      </form>


      <!-- Tabla -->
      <section class="tabla-ventas">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>ID Mesa</th>
              <th>ID Usuario</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($v = $ventas->fetch_assoc()): ?>
              <tr>
                <td><?= $v['id_venta'] ?></td>
                <td><?= $v['fecha_venta'] ?></td>
                <td>Q<?= number_format($v['total_venta'], 2) ?></td>
                <td><?= $v['id_mesa'] ?></td>
                <td><?= $v['id_usuario'] ?></td>
                <td class="acciones">
                  <a href="ventas.php?editar=<?= $v['id_venta'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                  <a href="ventas_crud.php?eliminar=<?= $v['id_venta'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="ventas_alertas.js"></script>
  <script src="ventas_form.js"></script>

</body>

</html>