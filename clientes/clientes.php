<?php
require_once '../conexion.php';
$db = conectar();

// Orden dinámico (por defecto DESC)
$orden = isset($_GET['orden']) && in_array($_GET['orden'], ['ASC', 'DESC'])
    ? $_GET['orden']
    : 'DESC';

// Obtener clientes con el orden elegido
$clientes = $db->query("
  SELECT id_cliente, nombre_cliente, apellido_cliente, dpi_cliente, telefono_cliente, direccion_cliente, correo_cliente
  FROM Clientes
  ORDER BY id_cliente $orden
");

// Cliente a editar
$clienteEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM Clientes WHERE id_cliente=$id");
  $clienteEditar = $res->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Clientes</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="clientes.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Clientes</h1>
      <h3>Gestión de Clientes</h3>

      <!-- Formulario -->
      <form method="POST" action="clientes_crud.php" class="formulario">
        <input type="hidden" name="id_cliente" value="<?= $clienteEditar['id_cliente'] ?? '' ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre_cliente" value="<?= $clienteEditar['nombre_cliente'] ?? '' ?>" required>

        <label>Apellido:</label>
        <input type="text" name="apellido_cliente" value="<?= $clienteEditar['apellido_cliente'] ?? '' ?>" required>

        <label>DPI:</label>
        <input type="text" name="dpi_cliente" value="<?= $clienteEditar['dpi_cliente'] ?? '' ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono_cliente" value="<?= $clienteEditar['telefono_cliente'] ?? '' ?>">

        <label>Dirección:</label>
        <input type="text" name="direccion_cliente" value="<?= $clienteEditar['direccion_cliente'] ?? '' ?>">

        <label>Correo:</label>
        <input type="email" name="correo_cliente" value="<?= $clienteEditar['correo_cliente'] ?? '' ?>">

        <div class="botones">
          <?php if ($clienteEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <a href="clientes.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
          <?php else: ?>
            <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
          <?php endif; ?>
        </div>
      </form>

<!-- Filtros -->
<div class="buscador">
  <input type="text" id="buscarCliente" placeholder="🔍 Buscar cliente">

  <label for="ordenClientes" style="margin-left:15px; font-weight:600;">Ordenar por ID:</label>
  <select id="ordenClientes">
    <option value="DESC" <?= ($orden == 'DESC') ? 'selected' : '' ?>>Más recientes primero</option>
    <option value="ASC" <?= ($orden == 'ASC') ? 'selected' : '' ?>>Más antiguos primero</option>
  </select>
</div>


      <!-- Tabla -->
      <section class="tabla">
        <table id="tablaClientes">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>DPI</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th>Correo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($c = $clientes->fetch_assoc()): ?>
              <tr>
                <td><?= $c['id_cliente'] ?></td>
                <td><?= $c['nombre_cliente'] ?></td>
                <td><?= $c['apellido_cliente'] ?></td>
                <td><?= $c['dpi_cliente'] ?></td>
                <td><?= $c['telefono_cliente'] ?></td>
                <td><?= $c['direccion_cliente'] ?></td>
                <td><?= $c['correo_cliente'] ?></td>
                <td class="acciones">
                  <a href="clientes.php?editar=<?= $c['id_cliente'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                  <a href="clientes_crud.php?eliminar=<?= $c['id_cliente'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="clientes_alertas.js"></script>
  <script src="clientes_form.js"></script>

<!-- Script de búsqueda y orden mejorado -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscarCliente");
    const filas = document.querySelectorAll("#tablaClientes tbody tr");
    const ordenSelect = document.getElementById("ordenClientes"); // 👈 nuevo combobox

    // ===== FILTRO DE BÚSQUEDA =====
    inputBuscar.addEventListener("keyup", () => {
      const filtro = inputBuscar.value.toLowerCase();
      filas.forEach(fila => {
        const nombre = fila.cells[1].textContent.toLowerCase();
        const apellido = fila.cells[2].textContent.toLowerCase();
        const dpi = fila.cells[3].textContent.toLowerCase();
        const telefono = fila.cells[4].textContent.toLowerCase();
        const correo = fila.cells[6].textContent.toLowerCase();

        if (
          nombre.includes(filtro) ||
          apellido.includes(filtro) ||
          dpi.includes(filtro) ||
          telefono.includes(filtro) ||
          correo.includes(filtro)
        ) {
          fila.style.display = "";
        } else {
          fila.style.display = "none";
        }
      });
    });

    // ===== CAMBIO DE ORDEN DE CLIENTES =====
    ordenSelect.addEventListener("change", () => {
      const orden = ordenSelect.value;
      // Recarga la página con el parámetro ?orden=ASC o ?orden=DESC
      window.location.href = `clientes.php?orden=${orden}`;
    });
  });
</script>
</body>
</html>
