<?php
require_once '../conexion.php';
$db = conectar();

// ============================ AGREGAR MESA ============================
if (isset($_POST['agregar'])) {
    $id_sucursal = (int)$_POST['id_sucursal'];
    $numero_mesa = (int)$_POST['numero_mesa'];
    $capacidad_mesa = $_POST['capacidad_mesa'] !== '' ? (int)$_POST['capacidad_mesa'] : null;
    $estado_mesa = trim($_POST['estado_mesa']);

    // Verificar duplicados: mismo numero_mesa dentro de la misma sucursal
    $verificar = $db->prepare("SELECT 1 FROM Mesas WHERE id_sucursal=? AND numero_mesa=?");
    $verificar->bind_param("ii", $id_sucursal, $numero_mesa);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: mesas.php?msg=duplicado");
        exit;
    }

    $stmt = $db->prepare("INSERT INTO Mesas (id_sucursal, numero_mesa, capacidad_mesa, estado_mesa) VALUES (?, ?, ?, ?)");
    if ($capacidad_mesa === null) {
        // setear NULL explícitamente
        $null = null;
        $stmt = $db->prepare("INSERT INTO Mesas (id_sucursal, numero_mesa, capacidad_mesa, estado_mesa) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $id_sucursal, $numero_mesa, $null, $estado_mesa);
    } else {
        $stmt->bind_param("iiis", $id_sucursal, $numero_mesa, $capacidad_mesa, $estado_mesa);
    }
    $stmt->execute();

    header("Location: mesas.php?msg=agregado");
    exit;
}

// ============================ EDITAR MESA ============================
if (isset($_POST['editar'])) {
    $id_mesas = (int)$_POST['id_mesas'];
    $id_sucursal = (int)$_POST['id_sucursal'];
    $numero_mesa = (int)$_POST['numero_mesa'];
    $capacidad_mesa = $_POST['capacidad_mesa'] !== '' ? (int)$_POST['capacidad_mesa'] : null;
    $estado_mesa = trim($_POST['estado_mesa']);

    // Verificar duplicados excluyendo el registro actual
    $verificar = $db->prepare("SELECT 1 FROM Mesas WHERE id_sucursal=? AND numero_mesa=? AND id_mesas<>?");
    $verificar->bind_param("iii", $id_sucursal, $numero_mesa, $id_mesas);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: mesas.php?msg=duplicado");
        exit;
    }

    $stmt = $db->prepare("UPDATE Mesas SET id_sucursal=?, numero_mesa=?, capacidad_mesa=?, estado_mesa=? WHERE id_mesas=?");
    if ($capacidad_mesa === null) {
        $null = null;
        $stmt->bind_param("iiisi", $id_sucursal, $numero_mesa, $null, $estado_mesa, $id_mesas);
    } else {
        $stmt->bind_param("iiisi", $id_sucursal, $numero_mesa, $capacidad_mesa, $estado_mesa, $id_mesas);
    }
    $stmt->execute();

    header("Location: mesas.php?msg=editado");
    exit;
}

// ============================ ELIMINAR MESA ============================
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $db->query("DELETE FROM Mesas WHERE id_mesas=$id");

    header("Location: mesas.php?msg=eliminado");
    exit;
}
?>
