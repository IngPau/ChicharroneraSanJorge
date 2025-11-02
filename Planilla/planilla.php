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

// Obtener empleados para el <select>
$empleados = $db->query("
  SELECT id_empleados, CONCAT(nombre_empleados, ' ', apellido_empleados) AS nombre_completo
  FROM Empleados
  ORDER BY nombre_empleados ASC
");

// Obtener todas las nóminas con nombre de empleado
$nominas = $db->query("
  SELECT n.*, CONCAT(e.nombre_empleados, ' ', e.apellido_empleados) AS nombre_empleado
  FROM Nomina n
  INNER JOIN Empleados e ON n.id_empleado = e.id_empleados
  ORDER BY n.id_nomina ASC
");

// Nómina a editar
$nominaEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("
    SELECT * FROM Nomina WHERE id_nomina=$id
  ");
  $nominaEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Planillas</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="planillas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container">
    <?php include_once '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Planillas</h1>
      <h3>Gestión de Nóminas</h3>

      <!-- Formulario -->
      <form method="POST" action="planillas_crud.php" class="formulario">
        <input type="hidden" name="id_nomina" value="<?= $nominaEditar['id_nomina'] ?? '' ?>">

        <label>Empleado:</label>
        <select name="id_empleado" required>
          <option value="">Seleccione un empleado</option>
          <?php while ($emp = $empleados->fetch_assoc()): ?>
            <option value="<?= $emp['id_empleados'] ?>"
              <?= isset($nominaEditar['id_empleado']) && $nominaEditar['id_empleado'] == $emp['id_empleados'] ? 'selected' : '' ?>>
              <?= $emp['nombre_completo'] ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Año:</label>
        <input type="number" name="año" value="<?= $nominaEditar['año'] ?? '' ?>" required>

        <label>Mes:</label>
        <input type="number" name="mes" value="<?= $nominaEditar['mes'] ?? '' ?>" required>

        <label>Sueldo Base:</label>
        <input type="number" step="0.01" name="sueldo_base" value="<?= $nominaEditar['sueldo_base'] ?? '' ?>" required>

        <div class="botones">
            <?php if ($nominaEditar): ?>
            <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <a href="planilla.php" class="btn btn-cancelar"><i class="fas fa-ban"></i> Cancelar</a>
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
              <th>Empleado</th>
              <th>Año</th>
              <th>Mes</th>
              <th>Sueldo Base</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($n = $nominas->fetch_assoc()): ?>
              <tr>
                <td><?= $n['id_nomina'] ?></td>
                <td><?= $n['nombre_empleado'] ?></td>
                <td><?= $n['año'] ?></td>
                <td><?= $n['mes'] ?></td>
                <td>Q <?= number_format($n['sueldo_base'], 2) ?></td>
                <td class="acciones">
                  <a href="planilla.php?editar=<?= $n['id_nomina'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                  <a href="planillas_crud.php?eliminar=<?= $n['id_nomina'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="planillas_alertas.js"></script>
</body>
</html>
