<?php
include_once '../../conexion.php';

$conn = conectar();
$sql = "SELECT nombre_sucursal FROM Sucursales";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<option value='" . $row["nombre_sucursal"] . "'>" . $row["nombre_sucursal"] . "</option>";
    }
} else {
    echo "<option value=''>No hay sucursales disponibles</option>";
}



$conn->close();
?>