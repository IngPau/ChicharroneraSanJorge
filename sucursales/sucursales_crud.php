<?php
require_once '../conexion.php';
$db = conectar();

// Agregar
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre_sucursal'];
    $telefono = $_POST['telefono_sucursal'];
    $direccion = $_POST['direccion_sucursal'];

    $stmt = $db->prepare("INSERT INTO Sucursales (nombre_sucursal, telefono_sucursal, direccion_sucursal) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $telefono, $direccion);
    $stmt->execute();

    header("Location: sucursales.php?msg=agregado");
    exit;
}

// Editar
if (isset($_POST['editar'])) {
    $id = $_POST['id_sucursal'];
    $nombre = $_POST['nombre_sucursal'];
    $telefono = $_POST['telefono_sucursal'];
    $direccion = $_POST['direccion_sucursal'];

    $stmt = $db->prepare("UPDATE Sucursales SET nombre_sucursal=?, telefono_sucursal=?, direccion_sucursal=? WHERE id_sucursal=?");
    $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $id);
    $stmt->execute();

    header("Location: sucursales.php?msg=editado");
    exit;
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->query("DELETE FROM Sucursales WHERE id_sucursal=$id");

    header("Location: sucursales.php?msg=eliminado");
    exit;
}
?>
