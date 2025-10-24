<?php
require_once '../conexion.php';
$db = conectar();

// Obtener empleados
$empleados = $db->query("
  SELECT e.id_empleados, e.nombre_empleados, e.apellido_empleados, e.dpi_empleados, e.telefono_empleados,
         p.nombre_puestos, s.nombre_sucursal
  FROM Empleados e
  LEFT JOIN Puestos p ON e.id_puesto = p.id_puesto
  LEFT JOIN Sucursales s ON e.id_sucursal = s.id_sucursal
  ORDER BY e.id_empleados DESC
");

// Empleado a editar
$empleadoEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM Empleados WHERE id_empleados=$id");
  $empleadoEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Empleados</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="empleados.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Empleados</h1>
      <h3>Gestión de Personal</h3>

      <!-- Formulario -->
      <form method="POST" action="empleados_crud.php" class="formulario">
        <input type="hidden" name="id_empleados" value="<?= $empleadoEditar['id_empleados'] ?? '' ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre_empleados" value="<?= $empleadoEditar['nombre_empleados'] ?? '' ?>" required>

        <label>Apellido:</label>
        <input type="text" name="apellido_empleados" value="<?= $empleadoEditar['apellido_empleados'] ?? '' ?>" required>

        <label>DPI:</label>
        <input type="text" name="dpi_empleados" value="<?= $empleadoEditar['dpi_empleados'] ?? '' ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono_empleados" value="<?= $empleadoEditar['telefono_empleados'] ?? '' ?>">

        <label>Puesto:</label>
        <select name="id_puesto" required>
          <option value="">-- Selecciona Puesto --</option>
          <?php
          $puestos = $db->query("SELECT * FROM Puestos");
          while ($p = $puestos->fetch_assoc()) {
            $selected = ($empleadoEditar['id_puesto'] ?? '') == $p['id_puesto'] ? 'selected' : '';
            echo "<option value='{$p['id_puesto']}' $selected>{$p['nombre_puestos']}</option>";
          }
          ?>
        </select>

        <label>Sucursal:</label>
        <select name="id_sucursal" required>
          <option value="">-- Selecciona Sucursal --</option>
          <?php
          $sucursales = $db->query("SELECT * FROM Sucursales");
          while ($s = $sucursales->fetch_assoc()) {
            $selected = ($empleadoEditar['id_sucursal'] ?? '') == $s['id_sucursal'] ? 'selected' : '';
            echo "<option value='{$s['id_sucursal']}' $selected>{$s['nombre_sucursal']}</option>";
          }
          ?>
        </select>

        <div class="botones">
          <?php if ($empleadoEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <a href="empleados.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
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
              <th>Apellido</th>
              <th>DPI</th>
              <th>Teléfono</th>
              <th>Puesto</th>
              <th>Sucursal</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($e = $empleados->fetch_assoc()): ?>
              <tr>
                <td><?= $e['id_empleados'] ?></td>
                <td><?= $e['nombre_empleados'] ?></td>
                <td><?= $e['apellido_empleados'] ?></td>
                <td><?= $e['dpi_empleados'] ?></td>
                <td><?= $e['telefono_empleados'] ?></td>
                <td><?= $e['nombre_puestos'] ?></td>
                <td><?= $e['nombre_sucursal'] ?></td>
                <td class="acciones">
                  <a href="empleados.php?editar=<?= $e['id_empleados'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                  <a href="empleados_crud.php?eliminar=<?= $e['id_empleados'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="empleados_alertas.js"></script>
  <script src="empleados_form.js"></script>

</body>
</html>
