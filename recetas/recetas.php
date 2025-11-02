<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

// Obtener todas las recetas
$recetas = $db->query("
  SELECT r.id_receta, p.nombre_plato 
  FROM recetas r
  INNER JOIN platos p ON r.id_plato = p.id_plato
  ORDER BY r.id_receta DESC
");

// Receta a editar (si aplica)
$recetaEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM recetas WHERE id_receta=$id");
  $recetaEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Recetas</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../recetas/recetas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Recetas</h1>
      <h3>Gestión de Recetas</h3>

      <!-- Formulario -->
      <form method="POST" action="recetas_crud.php" class="formulario">
        <input type="hidden" name="id_receta" value="<?= $recetaEditar['id_receta'] ?? '' ?>">

        <label>Plato:</label>
        <select name="id_plato" required>
          <option value="">-- Selecciona un plato --</option>
          <?php
          $platos = $db->query("SELECT id_plato, nombre_plato FROM platos");
          while ($p = $platos->fetch_assoc()) {
            $selected = ($recetaEditar['id_plato'] ?? '') == $p['id_plato'] ? 'selected' : '';
            echo "<option value='{$p['id_plato']}' $selected>{$p['nombre_plato']}</option>";
          }
          ?>
        </select>

        <div class="bloque-detalle">
          <h3>Detalle de Receta</h3>
          <div id="detalle-container">
            <div class="detalle-item">
              <label>Insumo:</label>
              <select name="id_insumo[]" required>
                <option value="">-- Selecciona un insumo --</option>
                <?php
                $insumos = $db->query("SELECT id_materia_prima, nombre_insumos, unidad_medida FROM materiaprima");
                while ($i = $insumos->fetch_assoc()) {
                  echo "<option value='{$i['id_materia_prima']}' data-unidad='{$i['unidad_medida']}'>{$i['nombre_insumos']} ({$i['unidad_medida']})</option>";
                }
                ?>
              </select>

              <label>Cantidad:</label>
              <input type="number" name="cantidad[]" step="0.01" min="0" required>
            </div>
          </div>

          <button type="button" class="agregar-detalle" onclick="agregarDetalle()">➕ Agregar otro insumo</button>

          <div class="botones">
            <?php if ($recetaEditar): ?>
              <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
              <a href="recetas.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
            <?php else: ?>
              <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
            <?php endif; ?>
          </div>
        </div>
      </form>

      <!-- Tabla de recetas -->
      <section class="tabla-ventas">
        <h3>Listado de Recetas</h3>
        <table>
          <thead>
            <tr>
              <th>ID Receta</th>
              <th>Plato</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($r = $recetas->fetch_assoc()): ?>
              <tr>
                <td><?= $r['id_receta'] ?></td>
                <td><?= $r['nombre_plato'] ?></td>
                <td class="acciones">
                  <a href="recetas.php?editar=<?= $r['id_receta'] ?>"><i class="fas fa-edit"></i></a>
                  <a href="recetas_crud.php?eliminar=<?= $r['id_receta'] ?>" onclick="return confirm('¿Eliminar esta receta?')"><i class="fas fa-trash"></i></a>
                  <a href="detalle_receta.php?id=<?= $r['id_receta'] ?>" class="btn-detalle">Ver Detalle</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
    function agregarDetalle() {
      const container = document.getElementById('detalle-container');
      const nuevo = document.createElement('div');
      nuevo.classList.add('detalle-item');
      nuevo.innerHTML = `
        <label>Insumo:</label>
        <select name="id_insumo[]" required>
          <option value="">-- Selecciona un insumo --</option>
          <?php
          $insumos = $db->query("SELECT id_materia_prima, nombre_insumos, unidad_medida FROM materiaprima");
          while ($i = $insumos->fetch_assoc()) {
            echo "<option value='{$i['id_materia_prima']}' data-unidad='{$i['unidad_medida']}'>{$i['nombre_insumos']} ({$i['unidad_medida']})</option>";
          }
          ?>
        </select>

        <label>Cantidad:</label>
        <input type="number" name="cantidad[]" step="0.01" min="0" required>
      `;
      container.appendChild(nuevo);
    }
  </script>
</body>
</html>
