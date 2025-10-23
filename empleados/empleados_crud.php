<?php
require_once '../conexion.php';
$db = conectar();

if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre_empleados'];
    $apellido = $_POST['apellido_empleados'];
    $dpi = $_POST['dpi_empleados'];
    $telefono = $_POST['telefono_empleados'];
    $id_puesto = $_POST['id_puesto'];
    $id_sucursal = $_POST['id_sucursal'];

    $stmt = $db->prepare("INSERT INTO Empleados (nombre_empleados, apellido_empleados, dpi_empleados, telefono_empleados, id_puesto, id_sucursal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $nombre, $apellido, $dpi, $telefono, $id_puesto, $id_sucursal);
    $stmt->execute();

    header("Location: empleados.php?msg=agregado");
    exit;
}

if (isset($_POST['editar'])) {
    $id = $_POST['id_empleados'];
    $nombre = $_POST['nombre_empleados'];
    $apellido = $_POST['apellido_empleados'];
    $dpi = $_POST['dpi_empleados'];
    $telefono = $_POST['telefono_empleados'];
    $id_puesto = $_POST['id_puesto'];
    $id_sucursal = $_POST['id_sucursal'];

    $stmt = $db->prepare("UPDATE Empleados SET nombre_empleados=?, apellido_empleados=?, dpi_empleados=?, telefono_empleados=?, id_puesto=?, id_sucursal=? WHERE id_empleados=?");
    $stmt->bind_param("ssssiii", $nombre, $apellido, $dpi, $telefono, $id_puesto, $id_sucursal, $id);
    $stmt->execute();

    header("Location: empleados.php?msg=editado");
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->query("DELETE FROM Empleados WHERE id_empleados=$id");

    header("Location: empleados.php?msg=eliminado");
    exit;
}
?>
