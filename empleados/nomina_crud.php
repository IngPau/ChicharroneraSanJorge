<?php
require_once '../conexion.php';
$db = conectar();

// Agregar nómina
if (isset($_POST['agregarNomina'])) {
    $id_empleado = $_POST['id_empleado'];
    $año = $_POST['año'];
    $mes = $_POST['mes'];
    $sueldo_base = $_POST['sueldo_base'];

    $stmt = $db->prepare("INSERT INTO Nomina (id_empleado, año, mes, sueldo_base) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $id_empleado, $año, $mes, $sueldo_base);
    $stmt->execute();

    header("Location: empleados.php?msg=nomina_agregada");
    exit;
}

// Editar nómina
if (isset($_POST['editarNomina'])) {
    $id_nomina = $_POST['id_nomina'];
    $id_empleado = $_POST['id_empleado'];
    $año = $_POST['año'];
    $mes = $_POST['mes'];
    $sueldo_base = $_POST['sueldo_base'];

    $stmt = $db->prepare("UPDATE Nomina SET id_empleado=?, año=?, mes=?, sueldo_base=? WHERE id_nomina=?");
    $stmt->bind_param("iiidi", $id_empleado, $año, $mes, $sueldo_base, $id_nomina);
    $stmt->execute();

    header("Location: empleados.php?msg=nomina_editada");
    exit;
}

// Eliminar nómina
if (isset($_GET['eliminar'])) {
    $id_nomina = $_GET['eliminar'];
    $db->query("DELETE FROM Nomina WHERE id_nomina=$id_nomina");

    header("Location: empleados.php?msg=nomina_eliminada");
    exit;
}
?>
