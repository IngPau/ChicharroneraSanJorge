<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}
require_once '../conexion.php';
$db = conectar();

// Orden dinámico
$orden = isset($_GET['orden']) && in_array($_GET['orden'], ['ASC', 'DESC'])
  ? $_GET['orden']
  : 'DESC';

// Obtener puestos
$puestos = $db->query("SELECT * FROM Puestos ORDER BY id_puesto $orden");

// Puesto a editar
$puestoEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM Puestos WHERE id_puesto=$id");
  $puestoEditar = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo Puestos</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="puestos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">
  <?php include_once '../SideBar/sidebar.php'; ?>

  <main class="main">
    <h1>Módulo Puestos</h1>
    <h3>Gestión de Puestos</h3>

    <!-- Formulario -->
    <form method="POST" action="puestos_crud.php" class="formulario">
      <input type="hidden" name="id_puesto" value="<?= $puestoEditar['id_puesto'] ?? '' ?>">

      <label>Nombre del Puesto:</label>
      <input type="text" name="nombre_puestos" value="<?= $puestoEditar['nombre_puestos'] ?? '' ?>" required>

      <label>Descripción:</label>
      <input type="text" name="descripcion_puestos" value="<?= $puestoEditar['descripcion_puestos'] ?? '' ?>">

      <label>Salario Base:</label>
      <input type="number" step="0.01" name="salario_base_puestos" value="<?= $puestoEditar['salario_base_puestos'] ?? '' ?>" required>

      <div class="botones">
        <?php if ($puestoEditar): ?>
          <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
          <a href="puestos.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
        <?php else: ?>
          <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
        <?php endif; ?>
      </div>
    </form>

    <!-- Buscador -->
    <div class="buscador">
      <input type="text" id="buscarPuesto" placeholder="🔍 Buscar puesto...">
      <label for="ordenPuestos" style="margin-left:15px;font-weight:600;">Ordenar por ID:</label>
      <select id="ordenPuestos">
        <option value="DESC" <?= ($orden == 'DESC') ? 'selected' : '' ?>>Más recientes</option>
        <option value="ASC" <?= ($orden == 'ASC') ? 'selected' : '' ?>>Más antiguos</option>
      </select>
    </div>

    <!-- Tabla -->
    <section class="tabla">
      <table id="tablaPuestos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Salario Base</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($p = $puestos->fetch_assoc()): ?>
            <tr>
              <td><?= $p['id_puesto'] ?></td>
              <td><?= htmlspecialchars($p['nombre_puestos']) ?></td>
              <td><?= htmlspecialchars($p['descripcion_puestos']) ?></td>
              <td>Q <?= number_format($p['salario_base_puestos'], 2) ?></td>
              <td class="acciones">
                <a href="puestos.php?editar=<?= $p['id_puesto'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                <a href="puestos_crud.php?eliminar=<?= $p['id_puesto'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="puestos_alertas.js"></script>
<script src="puestos_form.js"></script>

<!-- Búsqueda y orden -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const buscar = document.getElementById("buscarPuesto");
  const filas = document.querySelectorAll("#tablaPuestos tbody tr");
  const ordenSelect = document.getElementById("ordenPuestos");

  buscar.addEventListener("keyup", () => {
    const filtro = buscar.value.toLowerCase();
    filas.forEach(fila => {
      const nombre = fila.cells[1].textContent.toLowerCase();
      const descripcion = fila.cells[2].textContent.toLowerCase();
      if (nombre.includes(filtro) || descripcion.includes(filtro)) {
        fila.style.display = "";
      } else {
        fila.style.display = "none";
      }
    });
  });

  ordenSelect.addEventListener("change", () => {
    const orden = ordenSelect.value;
    window.location.href = `puestos.php?orden=${orden}`;
  });
});
</script>
</body>
</html>
