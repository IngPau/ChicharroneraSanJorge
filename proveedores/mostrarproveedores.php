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
            extract($fila);
            $tabla .= "<tr>
                        <td>$id_proveedor</td> 
                        <td contenteditable='true' data-field='nombre'>$nombre_proveedor</td>
                        <td contenteditable='true' data-field='correo'>$correo_proveedor</td>
                        <td contenteditable='true' data-field='telefono'>$telefono_proveedor</td>
                        <td contenteditable='true' data-field='direccion'>$direccion_proveedor</td>
                        <td> 
                            <ion-icon name='create-outline' class='iconoEditar' onclick='editarDatos($numeroFila);'></ion-icon> 
                        </td>
                        <td> 
                            <ion-icon name='trash-outline' class='iconoEliminar' onclick='eliminarDatos($id_proveedor)'></ion-icon> 
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