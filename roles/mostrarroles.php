<?php
// Incluimos la conexión a la base de datos
include_once '../conexion.php'; 

$db = conectar();

// Consulta SQL: Seleccionamos el ID, Nombre, Descripción y Estado del rol.
$sql = "SELECT id_rol, nombre_rol, descripcion_rol
        FROM roles";

$searchTerm = null;
$stmt = null;
$bindParams = false;

// Si se recibe un término de búsqueda, filtramos por nombre o descripción
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    $searchTerm = '%' . $_REQUEST['search'] . '%';
    $sql .= " WHERE nombre_rol LIKE ? OR descripcion_rol LIKE ?";
    $bindParams = true;
}

// Preparamos la consulta
if ($stmt = $db->prepare($sql)) {
    
    if ($bindParams) {
        // Solo necesitamos dos placeholders para nombre y descripción
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    $tabla = "";
    $datos = []; // Array para almacenar los datos originales
    $numeroFila = 0;

    if ($resultado && $resultado->num_rows > 0) {
        $tabla .= "<table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOMBRE</th>
                                <th>DESCRIPCIÓN</th>
                                <th>EDITAR PERMISOS</th>
                                <th>ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>";
        
        while ($fila = $resultado->fetch_assoc()) {
            
            // Reemplazamos los campos de usuario por los campos de rol
            $tabla .= "<tr>
                        <td>".htmlspecialchars($fila['id_rol'], ENT_QUOTES, 'UTF-8')."</td> 
                        <td contenteditable='true' data-field='nombre_rol'>".htmlspecialchars($fila['nombre_rol'], ENT_QUOTES, 'UTF-8')."</td>
                        <td contenteditable='true' data-field='descripcion_rol'>".htmlspecialchars($fila['descripcion_rol'], ENT_QUOTES, 'UTF-8')."</td>
                        <td> 
                            <button class='boton editar' onclick='editarRol(".htmlspecialchars($fila['id_rol'], ENT_QUOTES, 'UTF-8').");'>Editar</button>
                        </td>
                        
                        <td> 
                            <button class='boton eliminar' onclick='eliminarRol(".htmlspecialchars($fila['id_rol'], ENT_QUOTES, 'UTF-8').");'>Eliminar</button>
                        </td>
                      </tr>";
            
            $datos[] = $fila;
            $numeroFila++;
        }
        $tabla .= "</tbody></table>";
        
    } else {
        $tabla = "<div>No se encontraron roles.</div>";
    }

    $stmt->close();
    
} else {
    $tabla = "<div>Error al preparar la consulta: " . $db->error . "</div>";
    $datos = [];
}

$db->close();

// Devolvemos en formato JSON (como espera el JS)
header('Content-Type: application/json');
echo json_encode([
    'tabla' => $tabla,
    'datos' => $datos
]);
?>