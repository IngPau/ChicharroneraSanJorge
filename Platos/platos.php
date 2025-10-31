<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

$orden = isset($_GET['orden']) && in_array($_GET['orden'], ['ASC', 'DESC']) ? $_GET['orden'] : 'DESC';

$platos = $db->query("
  SELECT p.id_plato, p.nombre_plato, p.descripcion_plato, p.precio_plato, c.nombre_categoria
  FROM platos p
  INNER JOIN categorias_plato c ON p.id_categoria = c.id_categoriaplato
  ORDER BY p.id_plato $orden
");

$platoEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM plato WHERE id_plato=$id");
  $platoEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo Platos</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../Platos/platos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Platos</h1>
      <h3>Gestión de Platos</h3>

      <!-- Formulario -->
      <form method="POST" action="platos_crud.php" class="formulario">
        <input type="hidden" name="id_plato" value="<?= $platoEditar['id_plato'] ?? '' ?>">

        <label>Nombre del Plato:</label>
        <input type="text" name="nombre_plato" value="<?= $platoEditar['nombre_plato'] ?? '' ?>" required>

        <label>Descripción:</label>
        <textarea name="descripcion_plato"><?= $platoEditar['descripcion_plato'] ?? '' ?></textarea>

        <label>Precio (Q):</label>
        <input type="number" step="0.01" name="precio_plato" value="<?= $platoEditar['precio_plato'] ?? '' ?>" required>

        <label>Categoría:</label>
        <select name="id_categoria" required>
          <option value="">-- Selecciona una categoría --</option>
          <?php
          $categorias = $db->query("SELECT id_categoriaplato, nombre_categoria FROM categorias_plato");
          while ($cat = $categorias->fetch_assoc()) {
            $selected = ($platoEditar['id_categoriaplato'] ?? '') == $cat['id_categoriaplato'] ? 'selected' : '';
            echo "<option value='{$cat['id_categoriaplato']}' $selected>{$cat['nombre_categoria']}</option>";
          }
          ?>
        </select>

        <div class="botones">
          <?php if ($platoEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <a href="platos.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
          <?php else: ?>
            <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
          <?php endif; ?>
        </div>
      </form>

      <!-- Filtros -->
      <div class="buscador">
        <input type="text" id="buscarPlato" placeholder="🔍 Buscar plato">
        <label for="ordenPlatos" style="margin-left:15px; font-weight:600;">Ordenar por ID:</label>
        <select id="ordenPlatos">
          <option value="DESC" <?= ($orden == 'DESC') ? 'selected' : '' ?>>Más recientes primero</option>
          <option value="ASC" <?= ($orden == 'ASC') ? 'selected' : '' ?>>Más antiguos primero</option>
        </select>
      </div>

      <!-- Tabla -->
      <section class="tabla">
        <table id="tablaPlatos">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio</th>
              <th>Categoría</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($p = $platos->fetch_assoc()): ?>
              <tr>
                <td><?= $p['id_plato'] ?></td>
                <td><?= $p['nombre_plato'] ?></td>
                <td><?= $p['descripcion_plato'] ?></td>
                <td>Q<?= number_format($p['precio_plato'], 2) ?></td>
                <td><?= $p['nombre_categoria'] ?></td>
                <td class="acciones">
                  <a href="platos.php?editar=<?= $p['id_plato'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                  <a href="platos_crud.php?eliminar=<?= $p['id_plato'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="platos_alertas.js"></script>
  <script src="platos_form.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const inputBuscar = document.getElementById("buscarPlato");
      const filas = document.querySelectorAll("#tablaPlatos tbody tr");
      const ordenSelect = document.getElementById("ordenPlatos");

      inputBuscar.addEventListener("keyup", () => {
        const filtro = inputBuscar.value.toLowerCase();
        filas.forEach(fila => {
          const nombre = fila.cells[1].textContent.toLowerCase();
          const descripcion = fila.cells[2].textContent.toLowerCase();
          const categoria = fila.cells[4].textContent.toLowerCase();

          if (
            nombre.includes(filtro) ||
            descripcion.includes(filtro) ||
            categoria.includes(filtro)
          ) {
            fila.style.display = "";
          } else {
            fila.style.display = "none";
          }
        });
      });

      ordenSelect.addEventListener("change", () => {
        const orden = ordenSelect.value;
        window.location.href = `platos.php?orden=${orden}`;
      });
    });
  </script>
</body>
</html>
