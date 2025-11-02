<?php
include_once '../conexion.php';

$db = conectar();

$sql = "SELECT u.id_usuario, u.nombre_usuario, u.correo_usuario, u.contraseña_usuario, r.nombre_rol 
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol";

$searchTerm = null;
$stmt = null;
$bindParams = false;

// Si se recibe un término de búsqueda
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    $searchTerm = '%' . $_REQUEST['search'] . '%';
    $sql .= " WHERE u.nombre_usuario LIKE ? OR u.correo_usuario LIKE ? OR r.nombre_rol LIKE ?";
    $bindParams = true;
}

// Preparamos la consulta
if ($stmt = $db->prepare($sql)) {
    
    if ($bindParams) {
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    $tabla = "";
    $datos = [];
    $numeroFila = 0;

    if ($resultado && $resultado->num_rows > 0) {
        $tabla .= "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOMBRE</th>
                            <th>CORREO</th>
                            <th>CONTRASEÑA</th>
                            <th>ROL</th>
                            <th>EDITAR</th>
                            <th>ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        while ($fila = $resultado->fetch_assoc()) {
            $tabla .= "<tr>
                        <td>".htmlspecialchars($fila['id_usuario'], ENT_QUOTES, 'UTF-8')."</td> 
                        <td contenteditable='true' data-field='nombre_usuario'>".htmlspecialchars($fila['nombre_usuario'], ENT_QUOTES, 'UTF-8')."</td>
                        <td contenteditable='true' data-field='correo_usuario'>".htmlspecialchars($fila['correo_usuario'], ENT_QUOTES, 'UTF-8')."</td>
                        <td contenteditable='true' data-field='contraseña_usuario'>".htmlspecialchars($fila['contraseña_usuario'], ENT_QUOTES, 'UTF-8')."</td>
                        <td data-field='nombre_rol'>".htmlspecialchars($fila['nombre_rol'], ENT_QUOTES, 'UTF-8')."</td>
                        <td> 
                            <button class='boton editar' onclick='abrirModalEdicion(".htmlspecialchars($fila['id_usuario'], ENT_QUOTES, 'UTF-8').");'>Editar</button>
                        </td>
                        <td> 
                            <button class='boton eliminar' onclick='eliminarusuarios(".htmlspecialchars($fila['id_usuario'], ENT_QUOTES, 'UTF-8').");'>Eliminar</button>
                        </td>
                    </tr>";
            $datos[] = $fila;
            $numeroFila++;
        }
        $tabla .= "</tbody></table>";
        
    } else {
        $tabla = "<div>No se encontraron usuarios.</div>";
    }

    $stmt->close();
    
} else {
    $tabla = "<div>Error al preparar la consulta: " . $db->error . "</div>";
    $datos = [];
}

$db->close();

// Devolvemos en formato JSON
header('Content-Type: application/json');
echo json_encode([
    'tabla' => $tabla,
    'datos' => $datos
]);
?>
