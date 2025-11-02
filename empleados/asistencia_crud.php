<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}

require_once '../conexion.php';
$db = conectar();

// AGREGAR ASISTENCIA
if (isset($_POST['agregarAsistencia'])) {
  $id_empleado = $_POST['id_empleado'];
  $fecha = $_POST['fecha'];
  $hora_entrada = $_POST['hora_entrada'];
  $hora_salida = $_POST['hora_salida'] ?? null;

  $query = "INSERT INTO Asistencia (id_empleado, fecha, hora_entrada, hora_salida)
            VALUES (?, ?, ?, ?)";
  $stmt = $db->prepare($query);
  $stmt->bind_param("isss", $id_empleado, $fecha, $hora_entrada, $hora_salida);
  
  if ($stmt->execute()) {
    // Redirigimos con parámetro en URL para que `empleados_alertas.js` lo lea
    header("Location: empleados.php?msg=agregarAsistencia");
    exit();
  } else {
    // En caso de error redirigimos indicando fallo (se mantiene genérico)
    header("Location: empleados.php?msg=error_asistencia");
    exit();
  }
  
  
}

// EDITAR ASISTENCIA
if (isset($_POST['editarAsistencia'])) {
  $id_asistencia = $_POST['id_asistencia'];
  $id_empleado = $_POST['id_empleado'];
  $fecha = $_POST['fecha'];
  $hora_entrada = $_POST['hora_entrada'];
  $hora_salida = $_POST['hora_salida'] ?? null;

  $query = "UPDATE Asistencia 
            SET id_empleado=?, fecha=?, hora_entrada=?, hora_salida=? 
            WHERE id_asistencia=?";
  $stmt = $db->prepare($query);
  $stmt->bind_param("isssi", $id_empleado, $fecha, $hora_entrada, $hora_salida, $id_asistencia);

  if ($stmt->execute()) {
    header("Location: empleados.php?msg=editarAsistencia");
    exit();
  } else {
    header("Location: empleados.php?msg=error_asistencia");
    exit();
  }

  
}

// ELIMINAR ASISTENCIA
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  $query = "DELETE FROM Asistencia WHERE id_asistencia=?";
  $stmt = $db->prepare($query);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    header("Location: empleados.php?msg=eliminar");
    exit();
  } else {
    header("Location: empleados.php?msg=error_asistencia");
    exit();
  }

}
?>
