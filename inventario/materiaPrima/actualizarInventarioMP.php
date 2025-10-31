<?php
include_once '../../conexion.php';

$conn = conectar();

$data = json_decode(file_get_contents('php://input'), true);

$campos = [];
$valores = [];
$tipos = '';
$codigo = isset($data['codigo']) ? (int)$data['codigo'] : 0;

if (isset($data['cantidad'])){
    $cantidadNueva = (float)$data['cantidad'];
    $campos[] = "stock = ?";
    $valores[] = $cantidadNueva;
    $tipos .= 'd';  
}

if (isset($data['unidadMedida'])){
    $campos[] = "unidad_medida = ?";
    $valores[] = $data['unidadMedida'];
    $tipos .= 's';  
}

if (isset($data['stockMinimo'])){
    $stockNuevo = (float)$data['stockMinimo'];
    $campos[] = "cantidad_minima = ?";
    $valores[] = $stockNuevo;
    $tipos .= 'd';  
}

if (!empty($campos)){
    $actualizarCampos = $conn->prepare("UPDATE Inventario_MateriaPrima SET " . implode(', ', $campos) . " WHERE id_inventario = ?");
    $valores[] = $codigo;
    $tipos .= 'i';
    $actualizarCampos->bind_param($tipos, ...$valores);
    $actualizarCampos->execute();
     if ($actualizarCampos->error) {
        $estado = 0;
    } else {
        $estado = 1;
    }
    $actualizarCampos->close();
    echo json_encode(['estado' => $estado]);
}
$conn->close();
?>