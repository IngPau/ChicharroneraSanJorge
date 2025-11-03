<?php
require_once '../conexion.php';
$db = conectar();

// ================= AGREGAR NÓMINA =================
if (isset($_POST['agregarNomina'])) {
    $id_empleado = $_POST['id_empleado'];
    $año = $_POST['año'];
    $mes = $_POST['mes'];

    // ✅ Insertar registro (el sueldo_base lo llena el trigger automáticamente)
    $stmt = $db->prepare("INSERT INTO Nomina (id_empleado, año, mes) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_empleado, $año, $mes);

    if ($stmt->execute()) {
        header("Location: planilla.php?msg=nomina_agregada");
    } else {
        header("Location: planilla.php?msg=error");
    }
    exit;
}

// ================= EDITAR NÓMINA =================
if (isset($_POST['editarNomina'])) {
    $id_nomina = $_POST['id_nomina'];
    $id_empleado = $_POST['id_empleado'];
    $año = $_POST['año'];
    $mes = $_POST['mes'];

    $stmt = $db->prepare("UPDATE Nomina SET id_empleado=?, año=?, mes=? WHERE id_nomina=?");
    $stmt->bind_param("iiii", $id_empleado, $año, $mes, $id_nomina);

    if ($stmt->execute()) {
        header("Location: planilla.php?msg=nomina_editada");
    } else {
        header("Location: planilla.php?msg=error");
    }
    exit;
}

// ================= ELIMINAR NÓMINA =================
if (isset($_GET['eliminar'])) {
    $id_nomina = intval($_GET['eliminar']); // Convertir a número entero por seguridad

    $stmt = $db->prepare("DELETE FROM Nomina WHERE id_nomina = ?");
    $stmt->bind_param("i", $id_nomina);

    if ($stmt->execute()) {
        header("Location: planilla.php?msg=nomina_eliminada");
    } else {
        header("Location: planilla.php?msg=error");
    }

    $stmt->close();
    exit;
}
