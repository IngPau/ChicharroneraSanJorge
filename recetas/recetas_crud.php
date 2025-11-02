<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();

// ==================== AGREGAR RECETA ====================
if (isset($_POST['agregar'])) {
    $id_plato = $_POST['id_plato'];
    $id_usuario = $_SESSION['usuario_id'];
    
    // Insertar en recetas
    $stmt = $db->prepare("INSERT INTO recetas (id_plato) VALUES (?)");
    $stmt->bind_param("i", $id_plato);
    $stmt->execute();
    $id_receta = $stmt->insert_id;

    // Insertar detalles de insumos
    $insumos = $_POST['id_insumo'];
    $cantidades = $_POST['cantidad'];

    for ($i = 0; $i < count($insumos); $i++) {
        if (!empty($insumos[$i]) && !empty($cantidades[$i])) {
            $stmt_det = $db->prepare("INSERT INTO detalle_receta (id_receta, id_insumo, cantidad) VALUES (?, ?, ?)");
            $stmt_det->bind_param("iid", $id_receta, $insumos[$i], $cantidades[$i]);
            $stmt_det->execute();
        }
    }

    header("Location: recetas.php");
    exit();
}

// ==================== EDITAR RECETA ====================
if (isset($_POST['editar'])) {
    $id_receta = $_POST['id_receta'];
    $id_plato = $_POST['id_plato'];

    // Actualizar receta
    $stmt = $db->prepare("UPDATE recetas SET id_plato = ? WHERE id_receta = ?");
    $stmt->bind_param("ii", $id_plato, $id_receta);
    $stmt->execute();

    // Borrar detalles existentes
    $db->query("DELETE FROM detalle_receta WHERE id_receta = $id_receta");

    // Insertar nuevos detalles
    $insumos = $_POST['id_insumo'];
    $cantidades = $_POST['cantidad'];

    for ($i = 0; $i < count($insumos); $i++) {
        if (!empty($insumos[$i]) && !empty($cantidades[$i])) {
            $stmt_det = $db->prepare("INSERT INTO detalle_receta (id_receta, id_insumo, cantidad) VALUES (?, ?, ?)");
            $stmt_det->bind_param("iid", $id_receta, $insumos[$i], $cantidades[$i]);
            $stmt_det->execute();
        }
    }

    header("Location: recetas.php");
    exit();
}

// ==================== ELIMINAR RECETA ====================
if (isset($_GET['eliminar'])) {
    $id_receta = $_GET['eliminar'];

    // Borrar detalles
    $db->query("DELETE FROM detalle_receta WHERE id_receta = $id_receta");

    // Borrar receta
    $db->query("DELETE FROM recetas WHERE id_receta = $id_receta");

    header("Location: recetas.php");
    exit();
}
?>
