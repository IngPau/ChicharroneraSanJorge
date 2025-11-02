<?php
include_once '../conexion.php'; 
header('Content-Type: application/json');

$db = conectar();

$sql = "SELECT id_permiso, nombre_permiso FROM permisos";

$searchTerm = null;
$stmt = null;
$bindParams = false;

if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    $searchTerm = '%' . $_REQUEST['search'] . '%';
    $sql .= " WHERE nombre_permiso LIKE ?";
    $bindParams = true;
}

if ($stmt = $db->prepare($sql)) {
    
    if ($bindParams) {
        $stmt->bind_param("s", $searchTerm); // CORRECCIÓN: Solo un parámetro
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
                                <th>NOMBRE DEL PERMISO</th>
                                <th>EDITAR</th>
                                <th>ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>";
        
        while ($fila = $resultado->fetch_assoc()) {
            $tabla .= "<tr>
                        <td>".htmlspecialchars($fila['id_permiso'], ENT_QUOTES, 'UTF-8')."</td> 
                        <td contenteditable='true' data-field='nombre_permiso'>".htmlspecialchars($fila['nombre_permiso'], ENT_QUOTES, 'UTF-8')."</td>
                        <td> 
                            <button class='boton editar' onclick='abrirFormularioEdicionPermiso(".htmlspecialchars($fila['id_permiso'], ENT_QUOTES, 'UTF-8').");'>Editar</button>
                        </td>
                        <td> 
                            <button class='boton eliminar' onclick='eliminarPermiso(".htmlspecialchars($fila['id_permiso'], ENT_QUOTES, 'UTF-8').");'>Eliminar</button>
                        </td>
                      </tr>";
            
            $datos[] = $fila;
            $numeroFila++;
        }
        $tabla .= "</tbody></table>";
        
    } else {
        $tabla = "<div>No se encontraron permisos.</div>";
    }

    $stmt->close();
    
} else {
    $tabla = "<div>Error al preparar la consulta: " . $db->error . "</div>";
    $datos = [];
}

$db->close();

echo json_encode([
    'tabla' => $tabla,
    'datos' => $datos
]);
?>