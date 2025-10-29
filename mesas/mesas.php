<?php
require_once '../conexion.php';
$db = conectar();

// Obtener sucursales para el select
$sucursales = $db->query("
  SELECT id_sucursal, nombre_sucursal
  FROM Sucursales
  ORDER BY nombre_sucursal ASC
");

// Obtener mesas (con nombre de sucursal)
$mesas = $db->query("
  SELECT m.id_mesas, m.id_sucursal, s.nombre_sucursal,
         m.numero_mesa, m.capacidad_mesa, m.estado_mesa
  FROM Mesas m
  LEFT JOIN Sucursales s ON s.id_sucursal = m.id_sucursal
  ORDER BY m.id_mesas DESC
");

// Mesa a editar
$mesaEditar = null;
if (isset($_GET['editar'])) {
  $id = (int)$_GET['editar'];
  $res = $db->query("
    SELECT id_mesas, id_sucursal, numero_mesa, capacidad_mesa, estado_mesa
    FROM Mesas WHERE id_mesas=$id
  ");
  $mesaEditar = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Mesas</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="mesas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Mesas</h1>
      <h3>Gestión de Mesas por Sucursal</h3>

      <!-- Formulario -->
      <form method="POST" action="mesas_crud.php" class="formulario">
        <input type="hidden" name="id_mesas" value="<?= $mesaEditar['id_mesas'] ?? '' ?>">

        <label>Sucursal:</label>
        <select name="id_sucursal" required>
          <option value="">-- Selecciona una sucursal --</option>
          <?php
            // rehacer el cursor de sucursales (ya que pudo consumirse arriba)
            // Nota: si tu driver no permite reuso, vuelve a consultar
            $sres = $db->query("SELECT id_sucursal, nombre_sucursal FROM Sucursales ORDER BY nombre_sucursal ASC");
            while ($s = $sres->fetch_assoc()):
              $sel = isset($mesaEditar['id_sucursal']) && (int)$mesaEditar['id_sucursal'] === (int)$s['id_sucursal'] ? 'selected' : '';
          ?>
            <option value="<?= $s['id_sucursal'] ?>" <?= $sel ?>>
              <?= htmlspecialchars($s['nombre_sucursal']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Número de mesa:</label>
        <input type="number" name="numero_mesa" min="1" value="<?= $mesaEditar['numero_mesa'] ?? '' ?>" required>

        <label>Capacidad (opcional):</label>
        <input type="number" name="capacidad_mesa" min="1" value="<?= $mesaEditar['capacidad_mesa'] ?? '' ?>">

        <label>Estado:</label>
        <select name="estado_mesa" required>
          <?php
            $estados = ['DISPONIBLE','OCUPADA','RESERVADA','INACTIVA'];
            $actual = $mesaEditar['estado_mesa'] ?? 'DISPONIBLE';
            foreach ($estados as $e):
              $sel = (strtoupper($actual) === $e) ? 'selected' : '';
          ?>
            <option value="<?= $e ?>" <?= $sel ?>><?= $e ?></option>
          <?php endforeach; ?>
        </select>

        <div class="botones">
          <?php if ($mesaEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <a href="mesas.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
          <?php else: ?>
            <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
          <?php endif; ?>
        </div>
      </form>

      <!-- Filtro por sucursal -->
      <div class="buscador">
        <label for="filtroSucursal" style="font-weight:600; margin-right:10px;">Filtrar por sucursal:</label>
        <select id="filtroSucursal">
          <option value="todas">Todas las sucursales</option>
          <?php
            // Recarga el listado de sucursales
            $sresFiltro = $db->query("SELECT id_sucursal, nombre_sucursal FROM Sucursales ORDER BY nombre_sucursal ASC");
            while ($s = $sresFiltro->fetch_assoc()):
          ?>
            <option value="<?= $s['nombre_sucursal'] ?>"><?= htmlspecialchars($s['nombre_sucursal']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>


      <!-- Tabla -->
      <section class="tabla">
        <table id="tablaMesas">
          <thead>
            <tr>
              <th>ID</th>
              <th>Sucursal</th>
              <th>Número</th>
              <th>Capacidad</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($m = $mesas->fetch_assoc()): ?>
              <tr>
                <td><?= $m['id_mesas'] ?></td>
                <td><?= htmlspecialchars($m['nombre_sucursal'] ?? '—') ?></td>
                <td><?= $m['numero_mesa'] ?></td>
                <td><?= $m['capacidad_mesa'] !== null ? (int)$m['capacidad_mesa'] : '—' ?></td>
                <td><?= htmlspecialchars($m['estado_mesa'] ?? '') ?></td>
                <td class="acciones">
                  <a href="mesas.php?editar=<?= $m['id_mesas'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                  <a href="mesas_crud.php?eliminar=<?= $m['id_mesas'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="mesas_alertas.js"></script>
  <script src="mesas_form.js"></script>

  <!-- Filtro dinámico por sucursal -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const filtro = document.getElementById("filtroSucursal");
      const filas = document.querySelectorAll("#tablaMesas tbody tr");

      filtro.addEventListener("change", () => {
        const valor = filtro.value.toLowerCase();

        filas.forEach(fila => {
          const sucursal = fila.cells[1].textContent.toLowerCase();
          if (valor === "todas" || sucursal === valor) {
            fila.style.display = "";
          } else {
            fila.style.display = "none";
          }
        });
      });
    });
  </script>
</body>
</html>
