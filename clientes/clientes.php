<?php
require_once '../conexion.php';
$db = conectar();

// Obtener clientes
$clientes = $db->query("SELECT * FROM Clientes ORDER BY id_cliente DESC");

// Cliente a editar (si aplica)
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
  <link rel="stylesheet" href="styleCli.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Clientes</h1>
      <h3>Gestión de Clientes</h3>

      <!-- Botón agregar -->
      <?php if (!$clienteEditar): ?>
        <div class="botones-header">
          <a href="?nuevo=1" class="btn btn-agregar"><i class="fas fa-plus"></i> Nuevo Cliente</a>
        </div>
      <?php endif; ?>

      <!-- Formulario -->
      <?php if (isset($_GET['nuevo']) || $clienteEditar): ?>
        <form method="POST" action="crudClientes.php" class="formulario">
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
              <a href="clientes.php" class="btn btn-cancelar"><i class="fas fa-arrow-left"></i> Cancelar</a>
            <?php endif; ?>
          </div>
        </form>
      <?php endif; ?>

      <!-- Tabla de clientes -->
      <section class="tabla-clientes">
        <table>
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
                  <a href="crudClientes.php?eliminar=<?= $c['id_cliente'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>

    </main>
  </div>
</body>
</html>
