<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

// Consulta de nóminas
$nominas = $db->query("
  SELECT n.id_nomina, n.id_empleado, n.año, n.mes, n.sueldo_base,
         e.nombre_empleados, e.apellido_empleados
  FROM Nomina n
  LEFT JOIN Empleados e ON n.id_empleado = e.id_empleados
  ORDER BY n.id_nomina DESC
");

// Editar nómina
$nominaEditar = null;
if (isset($_GET['editarNomina'])) {
  $id = $_GET['editarNomina'];
  $res = $db->query("SELECT * FROM Nomina WHERE id_nomina=$id");
  $nominaEditar = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Planilla</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="planillas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>
    <main class="main">
      <h1>Módulo de Planilla</h1>
      <h2>Gestión de Nóminas</h2>

      <!-- Formulario de nómina -->
      <form method="POST" action="planillas_crud.php" class="formulario">
        <h3><?= $nominaEditar ? 'Editar Nómina' : 'Registrar Nómina' ?></h3>
        <input type="hidden" name="id_nomina" value="<?= $nominaEditar['id_nomina'] ?? '' ?>">

        <label>Empleado:</label>
        <select name="id_empleado" id="empleadoSelect" required>
          <option value="">-- Selecciona Empleado --</option>
          <?php
          $emps = $db->query("SELECT e.id_empleados, e.nombre_empleados, e.apellido_empleados, p.salario_base_puestos FROM Empleados e JOIN Puestos p ON e.id_puesto = p.id_puesto");
          while ($emp = $emps->fetch_assoc()) {
            $selected = ($nominaEditar['id_empleado'] ?? '') == $emp['id_empleados'] ? 'selected' : '';
            echo "<option value='{$emp['id_empleados']}' data-sueldo='{$emp['salario_base_puestos']}' $selected>{$emp['nombre_empleados']} {$emp['apellido_empleados']}</option>";
          }
          ?>
        </select>

        <label>Año:</label>
        <input type="number" name="año" min="2000" max="2100" required value="<?= $nominaEditar['año'] ?? '' ?>">

        <label>Mes:</label>
        <input type="number" name="mes" min="1" max="12" required value="<?= $nominaEditar['mes'] ?? '' ?>">

        <label>Sueldo Base:</label>
        <input type="number" step="0.01" name="sueldo_base" id="sueldoBase" readonly value="<?= $nominaEditar['sueldo_base'] ?? '' ?>">

        <div class="botones">
          <?php if ($nominaEditar): ?>
            <button type="submit" name="editarNomina" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar Nómina</button>
            <a href="planilla.php" class="btn btn-cancelar"><i class="fas fa-times"></i> Cancelar</a>
          <?php else: ?>
            <button type="submit" name="agregarNomina" class="btn btn-agregar"><i class="fas fa-check"></i> Guardar Nómina</button>
          <?php endif; ?>
        </div>
      </form>

      <!-- Tabla de nómina -->
      <section class="tabla">
        <table>
          <thead>
            <tr><th>ID</th><th>Empleado</th><th>Año</th><th>Mes</th><th>Sueldo</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            <?php while ($n = $nominas->fetch_assoc()): ?>
              <tr>
                <td><?= $n['id_nomina'] ?></td>
                <td><?= $n['nombre_empleados'] ?> <?= $n['apellido_empleados'] ?></td>
                <td><?= $n['año'] ?></td>
                <td><?= $n['mes'] ?></td>
                <td>Q<?= number_format($n['sueldo_base'], 2) ?></td>
                <td class="acciones">
                  <a href="planilla.php?editarNomina=<?= $n['id_nomina'] ?>" class="btn btn-editar"><i class="fas fa-edit"></i></a>
                  <a href="planillas_crud.php?eliminar=<?= $n['id_nomina'] ?>" class="btn btn-eliminar"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script>
  // Cargar sueldo base automático
  const empleadoSelect = document.getElementById('empleadoSelect');
  if (empleadoSelect) {
    empleadoSelect.addEventListener('change', function() {
      const sueldo = this.options[this.selectedIndex].getAttribute('data-sueldo');
      document.getElementById('sueldoBase').value = sueldo || '';
    });
  }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="planillas_alertas.js"></script>
</body>
</html>
