<?php
session_start(); // Iniciar la sesión
//Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
  header("Location: ../login/login.php");
  exit();
}
?>

<?php
require_once '../conexion.php';
$db = conectar();

// Obtener sucursales (orden ascendente: 1,2,3...)
$sucursales = $db->query("
  SELECT * FROM Sucursales
  ORDER BY id_sucursal ASC
");

// Sucursal a editar
$sucursalEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM Sucursales WHERE id_sucursal=$id");
  $sucursalEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Sucursales</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="sucursales.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include_once '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Sucursales</h1>
      <h3>Gestión de Sucursales</h3>

      <!-- Formulario -->
      <form method="POST" action="sucursales_crud.php" class="formulario">
        <input type="hidden" name="id_sucursal" value="<?= $sucursalEditar['id_sucursal'] ?? '' ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre_sucursal" value="<?= $sucursalEditar['nombre_sucursal'] ?? '' ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono_sucursal" value="<?= $sucursalEditar['telefono_sucursal'] ?? '' ?>">

        <label>Dirección:</label>
        <input type="text" name="direccion_sucursal" value="<?= $sucursalEditar['direccion_sucursal'] ?? '' ?>" required>

        <div class="botones">
          <?php if ($sucursalEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <a href="sucursales.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
          <?php else: ?>
            <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
          <?php endif; ?>
        </div>
      </form>

<!-- Tabla -->
<section class="tabla">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>Dirección / Mapa</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($sucursales && $sucursales->num_rows > 0): ?>
        <?php while ($s = $sucursales->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($s['id_sucursal']) ?></td>
            <td><?= htmlspecialchars($s['nombre_sucursal']) ?></td>
            <td><?= htmlspecialchars($s['telefono_sucursal']) ?></td>
            <td>
              <?= htmlspecialchars($s['direccion_sucursal']) ?><br>
              <iframe
                width="300"
                height="200"
                style="border:0; margin-top:8px;"
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q=<?= urlencode($s['direccion_sucursal']) ?>&output=embed">
              </iframe>
            </td>
            <td class="acciones">
              <a href="sucursales.php?editar=<?= urlencode($s['id_sucursal']) ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
              <a href="sucursales_crud.php?eliminar=<?= urlencode($s['id_sucursal']) ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5">No hay sucursales registradas.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="sucursales_alertas.js"></script>

</body>
</html>
