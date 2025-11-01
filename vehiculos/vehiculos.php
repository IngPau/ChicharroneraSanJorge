<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}
require_once '../conexion.php';
$db = conectar();
$sql = "SELECT v.*, s.nombre_sucursal 
        FROM vehiculos v
        LEFT JOIN sucursales s ON v.id_sucursal = s.id_sucursal
        ORDER BY v.id_vehiculo DESC";
$vehiculos = $db->query($sql);
$vehiculoEditar = null;
if (isset($_GET['editar'])) {
  $id = $_GET['editar'];
  $res = $db->query("SELECT * FROM vehiculos WHERE id_vehiculo = $id");
  $vehiculoEditar = $res->fetch_assoc();
}
$sucursales = $db->query("SELECT id_sucursal, nombre_sucursal FROM sucursales");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Vehículos</title>
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="styleVehiculos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>
    <main class="main">
      <h1>Módulo Vehículos</h1>
      <h3>Gestión de Vehículos</h3>
      <?php if (!$vehiculoEditar && !isset($_GET['nuevo'])): ?>
        <div class="botones-header">
          <a href="?nuevo=1" class="btn btn-agregar"><i class="fas fa-plus"></i> Nuevo Vehículo</a>
        </div>
      <?php endif; ?>
      <?php if (isset($_GET['nuevo']) || $vehiculoEditar): ?>
        <form id="formVehiculo" class="formulario">
          <input type="hidden" name="id_vehiculo" value="<?= $vehiculoEditar['id_vehiculo'] ?? '' ?>">
          <div class="campo">
            <label>Placa:</label>
            <input type="text" name="placa" value="<?= $vehiculoEditar['placa'] ?? '' ?>" required>
          </div>
          <div class="campo">
            <label>Marca:</label>
            <input type="text" name="marca" value="<?= $vehiculoEditar['marca'] ?? '' ?>" required>
          </div>
          <div class="campo">
            <label>Modelo:</label>
            <input type="text" name="modelo" value="<?= $vehiculoEditar['modelo'] ?? '' ?>" required>
          </div>
          <div class="campo">
            <label>Año:</label>
            <input type="number" name="anio" value="<?= $vehiculoEditar['anio'] ?? '' ?>" min="1900" max="2100" required>
          </div>
          <div class="campo">
            <label>Tipo de Vehículo:</label>
            <input type="text" name="tipo_vehiculo" value="<?= $vehiculoEditar['tipo_vehiculo'] ?? '' ?>" required>
          </div>
          <div class="campo">
            <label>Estado:</label>
            <select name="estado_vehiculo" required>
              <?php
                $estado = $vehiculoEditar['estado_vehiculo'] ?? '';
                $opciones = ['Disponible', 'En Ruta', 'Mantenimiento'];
                foreach ($opciones as $op) {
                  $sel = ($estado == $op) ? 'selected' : '';
                  echo "<option value='$op' $sel>$op</option>";
                }
              ?>
            </select>
          </div>
          <div class="campo">
            <label>Sucursal:</label>
            <select name="id_sucursal" required>
              <option value="">Seleccionar...</option>
              <?php while ($s = $sucursales->fetch_assoc()): ?>
                <option value="<?= $s['id_sucursal'] ?>"
                  <?= ($vehiculoEditar && $vehiculoEditar['id_sucursal'] == $s['id_sucursal']) ? 'selected' : '' ?>>
                  <?= $s['nombre_sucursal'] ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="botones">
            <?php if ($vehiculoEditar): ?>
              <button type="submit" name="editar" class="btn btn-editar"><i class="fas fa-save"></i> Actualizar</button>
            <?php else: ?>
              <button type="submit" name="agregar" class="btn btn-agregar"><i class="fas fa-plus"></i> Agregar</button>
            <?php endif; ?>
            <a href="vehiculos.php" class="btn btn-cancelar"><i class="fas fa-arrow-left"></i> Cancelar</a>
          </div>
        </form>
      <?php endif; ?>
      <?php if (!$vehiculoEditar && !isset($_GET['nuevo'])): ?>
        <section class="tabla-vehiculos">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Sucursal</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($v = $vehiculos->fetch_assoc()): ?>
                <tr>
                  <td><?= $v['id_vehiculo'] ?></td>
                  <td><?= $v['placa'] ?></td>
                  <td><?= $v['marca'] ?></td>
                  <td><?= $v['modelo'] ?></td>
                  <td><?= $v['anio'] ?></td>
                  <td><?= $v['tipo_vehiculo'] ?></td>
                  <td><?= $v['estado_vehiculo'] ?></td>
                  <td><?= $v['nombre_sucursal'] ?? 'Sin asignar' ?></td>
                  <td class="acciones">
                    <a href="vehiculos.php?editar=<?= $v['id_vehiculo'] ?>" class="btn btn-editar" title="Editar"><i class="fas fa-edit"></i></a>
                    <a href="CRUD_Vehiculos.php?eliminar=<?= $v['id_vehiculo'] ?>" class="btn btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </section>
      <?php endif; ?>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="vehiculos.js"></script>
</body>
</html>
