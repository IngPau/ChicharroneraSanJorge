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

// Obtener nóminas
$nominas = $db->query("
  SELECT n.id_nomina, n.id_empleado, n.año, n.mes, n.sueldo_base, 
         e.nombre_empleados, e.apellido_empleados
  FROM Nomina n
  LEFT JOIN Empleados e ON n.id_empleado = e.id_empleados
  ORDER BY n.id_nomina DESC
");

// Empleado a editar
$empleadoEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM Empleados WHERE id_empleados=$id");
  $empleadoEditar = $res->fetch_assoc();
}

// Nómina a editar
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

      <!-- Formulario de empleados -->
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
            <button type="button" id="btnNomina" class="btn btn-nomina"><i class="fas fa-money-bill"></i> Nómina</button>
          <?php endif; ?>
        </div>
      </form>

      <!-- Formulario de nómina (oculto por defecto) -->
      <form method="POST" action="nomina_crud.php" id="formNomina" class="formulario nomina-form" style="<?= $nominaEditar ? 'display:grid;' : 'display:none;' ?>">
  <h3><?= $nominaEditar ? 'Editar Nómina' : 'Registrar Nómina' ?></h3>
  
  <input type="hidden" name="id_nomina" value="<?= $nominaEditar['id_nomina'] ?? '' ?>">

  <label>Empleado:</label>
  <select name="id_empleado" required>
    <option value="">-- Selecciona Empleado --</option>
    <?php
    $emps = $db->query("SELECT id_empleados, nombre_empleados, apellido_empleados FROM Empleados");
    while ($emp = $emps->fetch_assoc()) {
      $selected = ($nominaEditar['id_empleado'] ?? '') == $emp['id_empleados'] ? 'selected' : '';
      echo "<option value='{$emp['id_empleados']}' $selected>{$emp['nombre_empleados']} {$emp['apellido_empleados']}</option>";
    }
    ?>
  </select>

  <label>Año:</label>
  <input type="number" name="año" min="2000" max="2100" required value="<?= $nominaEditar['año'] ?? '' ?>">

  <label>Mes:</label>
  <input type="number" name="mes" min="1" max="12" required value="<?= $nominaEditar['mes'] ?? '' ?>">

  <label>Sueldo Base:</label>
  <input type="number" step="0.01" name="sueldo_base" required value="<?= $nominaEditar['sueldo_base'] ?? '' ?>">

  <div class="botones">
    <?php if ($nominaEditar): ?>
      <button type="submit" name="editarNomina" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar Nómina</button>
      <button type="button" id="cerrarNomina" class="btn btn-cancelar"><i class="fas fa-times"></i> Cerrar</button>
    <?php else: ?>
      <button type="submit" name="agregarNomina" class="btn btn-agregar"><i class="fas fa-check"></i> Guardar Nómina</button>
      <button type="button" id="cerrarNomina" class="btn btn-cancelar"><i class="fas fa-times"></i> Cerrar</button>
    <?php endif; ?>
  </div>
</form>


      <!-- Tabla de empleados -->
      <section class="tabla">
        <h3>Empleados Registrados</h3>
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

      <!-- Tabla de nómina -->
<section class="tabla">
  <h3>Registro de Nómina</h3>
  <table>
    <thead>
      <tr>
        <th>ID Nómina</th>
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
          <td><?= $n['nombre_empleados'] ?> <?= $n['apellido_empleados'] ?></td>
          <td><?= $n['año'] ?></td>
          <td><?= $n['mes'] ?></td>
          <td>Q<?= number_format($n['sueldo_base'],2) ?></td>
          <td class="acciones">
            <a href="empleados.php?editarNomina=<?= $n['id_nomina'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
            <a href="nomina_crud.php?eliminar=<?= $n['id_nomina'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</section>

    </main>
  </div>

  <script>
    const btnNomina = document.getElementById('btnNomina');
    const formNomina = document.getElementById('formNomina');
    const cerrarNomina = document.getElementById('cerrarNomina');

    btnNomina.addEventListener('click', () => {
      formNomina.style.display = 'grid';
      btnNomina.style.display = 'none';
    });

    cerrarNomina.addEventListener('click', () => {
      formNomina.style.display = 'none';
      btnNomina.style.display = 'inline-flex';
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="empleados_alertas.js"></script>
</body>
</html>
