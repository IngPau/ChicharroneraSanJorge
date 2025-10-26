<?php
require_once '../conexion.php';
$db = conectar();

// ============================ AGREGAR CLIENTE ============================
if (isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre_cliente']);
    $apellido = trim($_POST['apellido_cliente']);
    $dpi = trim($_POST['dpi_cliente']);
    $telefono = trim($_POST['telefono_cliente']);
    $direccion = trim($_POST['direccion_cliente']);
    $correo = trim($_POST['correo_cliente']);

    // Verificar duplicados
    $verificar = $db->prepare("SELECT * FROM Clientes WHERE dpi_cliente=? OR telefono_cliente=? OR correo_cliente=?");
    $verificar->bind_param("sss", $dpi, $telefono, $correo);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: clientes.php?msg=duplicado");
        exit;
    }

    $stmt = $db->prepare("INSERT INTO Clientes (nombre_cliente, apellido_cliente, dpi_cliente, telefono_cliente, direccion_cliente, correo_cliente) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $dpi, $telefono, $direccion, $correo);
    $stmt->execute();

    header("Location: clientes.php?msg=agregado");
    exit;
}

// ============================ EDITAR CLIENTE ============================
if (isset($_POST['editar'])) {
    $id = $_POST['id_cliente'];
    $nombre = trim($_POST['nombre_cliente']);
    $apellido = trim($_POST['apellido_cliente']);
    $dpi = trim($_POST['dpi_cliente']);
    $telefono = trim($_POST['telefono_cliente']);
    $direccion = trim($_POST['direccion_cliente']);
    $correo = trim($_POST['correo_cliente']);

    // Verificar duplicados (excluyendo el cliente actual)
    $verificar = $db->prepare("SELECT * FROM Clientes WHERE (dpi_cliente=? OR telefono_cliente=? OR correo_cliente=?) AND id_cliente<>?");
    $verificar->bind_param("sssi", $dpi, $telefono, $correo, $id);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: clientes.php?msg=duplicado");
        exit;
    }

    $stmt = $db->prepare("UPDATE Clientes SET nombre_cliente=?, apellido_cliente=?, dpi_cliente=?, telefono_cliente=?, direccion_cliente=?, correo_cliente=? WHERE id_cliente=?");
    $stmt->bind_param("ssssssi", $nombre, $apellido, $dpi, $telefono, $direccion, $correo, $id);
    $stmt->execute();

    header("Location: clientes.php?msg=editado");
    exit;
}

// ============================ ELIMINAR CLIENTE ============================
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->query("DELETE FROM Clientes WHERE id_cliente=$id");

    header("Location: clientes.php?msg=eliminado");
    exit;
}
?>
