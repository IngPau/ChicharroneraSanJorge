<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

// Validar ID de receta
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "ID de receta no válido.";
  exit();
}

$idReceta = intval($_GET['id']);

// Obtener el nombre del plato asociado a la receta
$resPlato = $db->query("
  SELECT p.nombre_plato
  FROM recetas r
  INNER JOIN platos p ON r.id_plato = p.id_plato
  WHERE r.id_receta = $idReceta
  LIMIT 1
");

$nombrePlato = $resPlato->fetch_assoc()['nombre_plato'] ?? 'Desconocido';

// Obtener detalle de insumos desde detalle_receta
$detalle = $db->query("
  SELECT mp.nombre_insumos, mp.unidad_medida, dr.cantidad
  FROM detalle_receta dr
  INNER JOIN materiaprima mp ON dr.id_insumo = mp.id_materia_prima
  WHERE dr.id_receta = $idReceta
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle de Receta</title>
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../recetas/detalle_receta.css">
</head>

<body>
  <div class="container">
    <main class="main">
      <h1>Detalle de Receta</h1>
      <h3>Plato: <?= htmlspecialchars($nombrePlato) ?></h3>

      <table>
        <thead>
          <tr>
            <th>Insumo</th>
            <th>Cantidad</th>
            <th>Unidad</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $detalle->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['nombre_insumos']) ?></td>
              <td><?= number_format($row['cantidad'], 2) ?></td>
              <td><?= htmlspecialchars($row['unidad_medida']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <a href="recetas.php" class="btn btn-volver">← Volver al módulo de recetas</a>
    </main>
  </div>
</body>
</html>
