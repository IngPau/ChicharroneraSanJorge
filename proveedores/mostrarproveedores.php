<?php
include_once '../conexion.php';

$db = conectar();

$sql = "SELECT id_proveedor, nombre_proveedor, correo_proveedor, telefono_proveedor, direccion_proveedor FROM proveedores";

$searchTerm = null;
$stmt = null;
$bindParams = false;

if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    $searchTerm = '%' . $_REQUEST['search'] . '%';
    
    $sql .= " WHERE nombre_proveedor LIKE ?";
    $bindParams = true;
}

if ($stmt = $db->prepare($sql)) {
    
    if ($bindParams) {
        $stmt->bind_param("s", $searchTerm);
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
                            <th>TELEFONO</th>
                            <th>DIRECCION</th>
                            <th>EDITAR</th>
                            <th>ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        while ($fila = $resultado->fetch_assoc()) {
            $tabla .= "<tr>
                        <td>".htmlspecialchars($fila['id_proveedor'], ENT_QUOTES, 'UTF-8')."</td> 
                        <td contenteditable='true' data-field='nombre'>".htmlspecialchars($fila['nombre_proveedor'], ENT_QUOTES, 'UTF-8')."</td>
                        <td contenteditable='true' data-field='correo'>".htmlspecialchars($fila['correo_proveedor'], ENT_QUOTES, 'UTF-8')."</td>
                        <td contenteditable='true' data-field='telefono'>".htmlspecialchars($fila['telefono_proveedor'], ENT_QUOTES, 'UTF-8')."</td>
                        <td contenteditable='true' data-field='direccion'>".htmlspecialchars($fila['direccion_proveedor'], ENT_QUOTES, 'UTF-8')."</td>
                        <td> 
                            <button class='boton editar' onclick='editarDatos(".htmlspecialchars($numeroFila, ENT_QUOTES, 'UTF-8').");'>Editar</button>
                        </td>
                        <td> 
                            <button class='boton eliminar' onclick='eliminarDatos(".htmlspecialchars($fila['id_proveedor'], ENT_QUOTES, 'UTF-8').");'>Eliminar</button>
                        </td>
                    </tr>";
            $datos[] = $fila;
            $numeroFila++;
        }
        $tabla .= "</tbody></table>";
        
    } else {
        $tabla = "<div>No se encontraron proveedores.</div>";
    }

    $stmt->close();
    
} else {
    $tabla = "<div>Error al preparar la consulta: " . $db->error . "</div>";
    $datos = [];
}


$db->close();

header('Content-Type: application/json');
echo json_encode([
    'tabla' => $tabla,
    'datos' => $datos
]);
?>