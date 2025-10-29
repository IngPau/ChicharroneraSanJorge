<?php
include_once '../conexion.php';

$db = conectar();

$sql = "SELECT id_pago_proveedor, id_compra, fecha_pago_proveedor, monto_pago_proveedor FROM pagos_proveedor";

$searchTerm = null;
$stmt = null;
$bindParams = false;

if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    $searchTerm = '%' . $_REQUEST['search'] . '%';

    $sql .= " WHERE id_compra LIKE ?";
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
                            <th>COMPRA ID</th>
                            <th>FECHA</th>
                            <th>MONTO</th>
                            <th>EDITAR</th>
                            <th>ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        while ($fila = $resultado->fetch_assoc()) {
            extract($fila);
            $tabla .= "<tr>
                        <td>$id_pago_proveedor</td> 
                        <td contenteditable='true' data-field='id_compra'>$id_compra</td>
                        <td contenteditable='true' data-field='fecha'>$fecha_pago_proveedor</td>
                        <td contenteditable='true' data-field='monto'>$monto_pago_proveedor</td>
                        <td> 
                            <ion-icon name='create-outline' class='iconoEditar' onclick='editarDatos($numeroFila);'></ion-icon> 
                        </td>
                        <td> 
                            <ion-icon name='trash-outline' class='iconoEliminar' onclick='eliminarDatos($id_pago_proveedor)'></ion-icon> 
                        </td>
                    </tr>";
            $datos[] = $fila;
            $numeroFila++;
        }
        $tabla .= "</tbody></table>";
        
    } else {
        $tabla = "<div>No se encontraron pagos.</div>";
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