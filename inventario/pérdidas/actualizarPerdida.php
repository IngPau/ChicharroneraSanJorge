<?php
include_once '../../conexion.php';

$conn = conectar();

$data = json_decode(file_get_contents('php://input'), true);

$campos = [];
$valores = [];
$tipos = '';
$codigo = isset($data['codigo']) ? (int)$data['codigo'] : 0;

$consultaPerdida = $conn->prepare("SELECT id_materia_prima, id_almacen FROM Perdidas WHERE id_perdida = ?");
$consultaPerdida->bind_param('i', $codigo);
$consultaPerdida->execute();
$consultaPerdida->bind_result($idMateriaPrima, $idAlmacen);
if (!$consultaPerdida->fetch()) {
    echo json_encode(['estado' => 0, 'error' => 'Registro de pérdida no encontrado']);
    $consultaPerdida->close();
    $conn->close();
    exit;
}
$consultaPerdida->close();


if (isset($data['cantidad'])){
    $cantidadNueva = (float)$data['cantidad'];
    $cantidadAnterior = (float)$data['cantidadAnterior'];
    $diferencia = $cantidadNueva - $cantidadAnterior;
    if ($diferencia > 0) {
        // Consultar stock actual
        $consultaStock = $conn->prepare("
            SELECT stock 
            FROM Inventario_MateriaPrima 
            WHERE id_insumo = ? AND id_almacen = ?
        ");
        $consultaStock->bind_param('ii', $idMateriaPrima, $idAlmacen);
        $consultaStock->execute();
        $resultadoStock = $consultaStock->get_result();
        $filaStock = $resultadoStock->fetch_assoc();
        $cantidadActual = (float)$filaStock['stock'];
        $consultaStock->close();

        if ($cantidadActual < $diferencia) {
            // No hay suficiente inventario → cancelar
            echo json_encode(['estado' => 2, 'error' => 'Stock insuficiente para aumentar la pérdida']);
            exit;
        }
    }
    if ($diferencia > 0) {
        // Restar la diferencia
        $actualizarInventario = $conn->prepare("
            UPDATE Inventario_MateriaPrima 
            SET stock = stock - ? 
            WHERE id_insumo = ? AND id_almacen = ?
        ");
        $actualizarInventario->bind_param('dii', $diferencia, $idMateriaPrima, $idAlmacen);
    } elseif ($diferencia < 0) {
        // Sumar la diferencia
        $cantidadSumar = abs($diferencia);
        $actualizarInventario = $conn->prepare("
            UPDATE Inventario_MateriaPrima 
            SET stock = stock + ? 
            WHERE id_insumo = ? AND id_almacen = ?
        ");
        $actualizarInventario->bind_param('dii', $cantidadSumar, $idMateriaPrima, $idAlmacen);
    }

    if (isset($actualizarInventario)) {
        $actualizarInventario->execute();
        $actualizarInventario->close();
    }


    $cantidadNueva = (float)$data['cantidad'];
    $campos[] = "cantidad = ?";
    $valores[] = $cantidadNueva;
    $tipos .= 'd';  
}

if (isset($data['motivo'])){
    $campos[] = "motivo = ?";
    $valores[] = $data['motivo'];
    $tipos .= 's';  
}

if (isset($data['Fecha'])){
    $campos[] = "fecha = ?";
    $valores[] = $data['Fecha'];
    $tipos .= 's';  
}

if (!empty($campos)){
    $actualizarCampos = $conn->prepare("UPDATE Perdidas SET " . implode(', ', $campos) . " WHERE id_perdida = ?");
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