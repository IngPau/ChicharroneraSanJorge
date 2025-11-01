<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../conexion.php";
require_once "api_key.php";
$db = conectar();

header("Content-Type: application/json");

// CONFIGURACIÓN DE LA API DE OPENAI
$apiKey = $OPENAI_API_KEY ?? '';

if (empty($apiKey)) {
    echo json_encode(["error" => "No se encontró la API Key."]);
    exit;
}


// LECTURA DE PREGUNTA

$input = json_decode(file_get_contents("php://input"), true);
$pregunta = trim($input["pregunta"] ?? "");

if (!$pregunta) {
    echo json_encode(["error" => "⚠️ La pregunta está vacía."]);
    exit;
}


// GENERACIÓN DE CONSULTA SQL SEGURA

$promptSQL = "
Eres un asistente SQL experto en bases de datos de restaurantes.

Objetivo:
Generar una consulta SQL válida en MySQL basada en la pregunta del usuario.

Reglas IMPORTANTES:
SOLO puedes generar consultas SELECT (también anidados y con JOIN, GROUP BY o HAVING).
Devuelve SIEMPRE exactamente 2 columnas: una categoría (texto) y una métrica numérica (por ejemplo COUNT, SUM, AVG).
Usa nombres claros para las columnas (por ejemplo, 'EstadoVehiculo', 'Cantidad', 'Sucursal', 'TotalVentas').
No uses INSERT, UPDATE, DELETE, DROP, CREATE, ALTER, TRUNCATE, REPLACE ni comandos que modifiquen datos.
Si el usuario pide modificar datos, responde con un SELECT simbólico o ignora la acción.

Tablas principales y relaciones:
- ventas(id_venta, id_usuario, id_cliente, id_mesa, fecha_venta, total_venta, id_sucursal)
- detalle_venta(id_detalle, id_venta, id_plato, cantidad, precio_unitario)
- platos(id_plato, nombre_plato, descripcion_plato, precio_plato, id_categoria)
- categorias_plato(id_categoriaplato, nombre_categoria, descripcion_categoria)
- sucursales(id_sucursal, nombre_sucursal, telefono_sucursal, direccion_sucursal)
- clientes(id_cliente, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente)
- empleados(id_empleados, nombre_empleados, apellido_empleados, id_puesto, id_sucursal)
- puestos(id_puesto, nombre_puestos, salario_base_puestos)
- inventario_materiaprima(id_inventario, id_almacen, id_insumo, stock, cantidad_minima, unidad_medida)
- materiaprima(id_materia_prima, nombre_insumos, unidad_medida, id_categoria)
- categoria_insumos(id_categoria, nombre_categoria, descripcion_categoria)
- perdidas(id_perdida, id_materia_prima, id_almacen, cantidad, fecha, motivo)
- proveedores(id_proveedor, nombre_proveedor, telefono_proveedor, correo_proveedor)
- compra(id_compra, id_proveedor, fecha_compra, total_compra, estado_compra)
- detalle_compra(id_detalle_compra, id_compra, id_insumo, cantidad_insumo, precio_unitario)
- vehiculos(id_vehiculo, placa, marca, modelo, tipo_vehiculo, estado_vehiculo, id_sucursal)
- asignacion_vehiculo(id_asignacion, id_empleado, id_vehiculo, id_ruta, fecha_asignacion, fecha_retorno)
- rutas(id_ruta, nombre_ruta, origen, destino, distancia_km)

Relaciones importantes:
- ventas → detalle_venta → platos → categorias_plato
- ventas → sucursales
- empleados → puestos → sucursales
- inventario_materiaprima → materiaprima → categoria_insumos
- perdidas → materiaprima
- compra → detalle_compra → materiaprima
- asignacion_vehiculo → empleados, vehiculos, rutas

Pregunta del usuario: \"$pregunta\"

Devuelve SOLO la consulta SQL sin explicaciones ni texto adicional.
";

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "Eres un generador de consultas SQL seguras para sistemas de restaurantes. Solo devuelves SELECTs válidos."],
            ["role" => "user", "content" => $promptSQL]
        ]
    ])
]);
$responseSQL = curl_exec($ch);
curl_close($ch);

$dataSQL = json_decode($responseSQL, true);
$sql = trim(str_replace(["```sql", "```"], "", $dataSQL["choices"][0]["message"]["content"] ?? ""));

if (!$sql) {
    echo json_encode(["error" => "No se pudo generar la consulta SQL."]);
    exit;
}

// =====================================================
// 2️⃣ FILTRO DE SEGURIDAD
// =====================================================
$consultaLimpia = strtoupper($sql);
$palabrasProhibidas = ["INSERT", "UPDATE", "DELETE", "DROP", "CREATE", "ALTER", "TRUNCATE", "REPLACE", "RENAME", "GRANT", "REVOKE", "SET", "USE"];

if (!preg_match('/^SELECT\s+/i', $sql)) {
    echo json_encode(["error" => "Solo se permiten consultas SELECT."]);
    exit;
}
foreach ($palabrasProhibidas as $peligrosa) {
    if (stripos($consultaLimpia, $peligrosa) !== false) {
        echo json_encode(["error" => "La consulta contiene una palabra no permitida: $peligrosa"]);
        exit;
    }
}


//EJECUCIÓN DE CONSULTA

$result = @$db->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode([
        "chartType" => "bar",
        "chartData" => ["labels" => [], "datasets" => []],
        "interpretacion" => "No se encontraron resultados.",
        "titulo" => "Sin datos",
        "consultaSQL" => $sql
    ]);
    exit;
}

$labels = [];
$values = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = array_values($row)[0];
    $values[] = (float) array_values($row)[1];
}


//INTERPRETACIÓN + TÍTULO AUTOMÁTICO DE LA IA

$promptInterpret = "
Analiza los siguientes datos obtenidos de una consulta SQL en un restaurante:
Etiquetas: " . json_encode($labels) . "
Valores: " . json_encode($values) . "
Consulta original: \"$pregunta\"

Devuelve una respuesta en formato JSON con las siguientes claves:
{
  \"titulo\": \"(título breve del gráfico, máximo 5 palabras)\",
  \"analisis\": \"(explicación breve y natural de los datos en español)\"
}
";

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "gpt-4o-mini",
        "response_format" => ["type" => "json_object"],
        "messages" => [
            ["role" => "system", "content" => "Eres un analista de datos que interpreta resultados y asigna títulos descriptivos a gráficos."],
            ["role" => "user", "content" => $promptInterpret]
        ]
    ])
]);
$responseIA = curl_exec($ch);
curl_close($ch);

$dataIA = json_decode($responseIA, true);
$interpretacionJSON = json_decode($dataIA["choices"][0]["message"]["content"] ?? "{}", true);

$tituloGrafico = $interpretacionJSON["titulo"] ?? "Visualización de Datos";
$interpretacion = $interpretacionJSON["analisis"] ?? "No se pudo generar interpretación.";


//RESPUESTA AL FRONTEND

echo json_encode([
    "chartType" => "bar",
    "chartData" => [
        "labels" => $labels,
        "datasets" => [[
            "label" => $tituloGrafico,
            "data" => $values,
            "backgroundColor" => "rgba(220,38,38,0.7)",
            "borderColor" => "#b91c1c",
            "borderWidth" => 1
        ]]
    ],
    "interpretacion" => $interpretacion,
    "titulo" => $tituloGrafico,
    "consultaSQL" => $sql
]);
?>
