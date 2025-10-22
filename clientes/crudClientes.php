<?php
require_once '../conexion.php';
$db = conectar();

// AGREGAR CLIENTE
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre_cliente'];
    $apellido = $_POST['apellido_cliente'];
    $dpi = $_POST['dpi_cliente'];
    $telefono = $_POST['telefono_cliente'];
    $direccion = $_POST['direccion_cliente'];
    $correo = $_POST['correo_cliente'];

    $sql = "INSERT INTO Clientes (nombre_cliente, apellido_cliente, dpi_cliente, telefono_cliente, direccion_cliente, correo_cliente)
            VALUES ('$nombre', '$apellido', '$dpi', '$telefono', '$direccion', '$correo')";
    $db->query($sql);
    header("Location: clientes.php");
    exit;
}

// EDITAR CLIENTE
if (isset($_POST['editar'])) {
    $id = $_POST['id_cliente'];
    $nombre = $_POST['nombre_cliente'];
    $apellido = $_POST['apellido_cliente'];
    $dpi = $_POST['dpi_cliente'];
    $telefono = $_POST['telefono_cliente'];
    $direccion = $_POST['direccion_cliente'];
    $correo = $_POST['correo_cliente'];

    $sql = "UPDATE Clientes SET 
                nombre_cliente='$nombre', 
                apellido_cliente='$apellido', 
                dpi_cliente='$dpi', 
                telefono_cliente='$telefono', 
                direccion_cliente='$direccion', 
                correo_cliente='$correo'
            WHERE id_cliente=$id";
    $db->query($sql);
    header("Location: clientes.php");
    exit;
}

// ELIMINAR CLIENTE
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->query("DELETE FROM Clientes WHERE id_cliente=$id");
    header("Location: clientes.php");
    exit;
}
?>
