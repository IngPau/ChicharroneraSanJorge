<?php
include_once '../../conexion.php';

$conn = conectar();

$data = json_decode(file_get_contents('php://input'), true);

$estado = 0;
$campos = [];
$valores = [];
$tipos = '';
$codigo = isset($data['codigo']) ? (int)$data['codigo'] : 0;

if($data != null){
    if (isset($data['descripcion']) || isset($data['categoria'])){
        $consultaMobiliario = $conn->prepare(("SELECT id_mobiliario FROM Inventario_Mobiliario im WHERE im.id_inventario_mobiliario= ?"));
        $consultaMobiliario->bind_param('i', $codigo);
        $consultaMobiliario->execute();
        $consultaMobiliario->bind_result($codigoMobiliario);
        if (!$consultaMobiliario->fetch()) {
            $consultaMobiliario->close();
            echo json_encode(['estado' => 0, 'error' => 'Registro de inventario no encontrado']);
            $conn->close();
            exit;
        }
        $consultaMobiliario->close();
    }
    
    if (isset($data['categoria'])){
        $codigoCategoria = verificarExistencia('Categoria_Mobiliario','id_categoria_mobiliario', 'nombre_categoria', $data['categoria']);
        $campos[] = "id_categoria_mobiliario = ?";
        $valores[] = $codigoCategoria;
        $tipos .= 'i';  
    }
    if (isset($data['descripcion'])){
        $campos[] = "descripcion = ?";
        $valores[] = $data['descripcion'];
        $tipos .= 's';  
    }
    if (!empty($campos)){
        $actualizarCampos = $conn->prepare("UPDATE Mobiliario SET " . implode(', ', $campos) . " WHERE id_mobiliario = ?");
        $valores[] = $codigoMobiliario;
        $tipos .= 'i';
        $actualizarCampos->bind_param($tipos, ...$valores);
        $actualizarCampos->execute();
        if ($actualizarCampos->error) {
            $estado = 0;
        } else {
            $estado = 1;
        }
        $actualizarCampos->close();
    }
    if (isset($data['cantidad'])){
        $actualizarInventario = $conn->prepare("UPDATE Inventario_Mobiliario SET stock = ? WHERE id_inventario_mobiliario = ?");
        $actualizarInventario->bind_param("ii", $data['cantidad'], $codigo);
        $actualizarInventario->execute();
        $estado = $actualizarInventario->error ? 0 : 1;
        $actualizarInventario->close();
    }
    echo json_encode(['estado' => $estado]);
}


function verificarExistencia($tabla, $campoId, $atributo, $valor){
    global $conn;
    // Consultar si el atributo ya existe en la tabla
    $sql = "SELECT $campoId FROM $tabla WHERE $atributo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Si existe, devuelve el ID del atributo asignado
        $id = $resultado->fetch_assoc();
        $stmt->close();
        return $id[$campoId];
    } else {
        $stmt->close();
        // En caso contrario, se debe hacer un nuevo insert en la tabla correspondiente
        $insercionSQL = $conn->prepare("INSERT INTO $tabla($atributo) VALUES (?)");
        $insercionSQL->bind_param("s", $valor);
        $insercionSQL->execute();
        // Obtener el ID del nuevo registro insertado
        $insertedId = $conn->insert_id;
        $insercionSQL->close();
        return $insertedId;
    }
}

$conn->close();
?>
