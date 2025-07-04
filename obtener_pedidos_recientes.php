<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conexion = new mysqli("localhost", "root", "", "artepan");

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$query = "SELECT folio, total, hora_entrega FROM pedidos WHERE estado = 'pendiente' ORDER BY fecha DESC LIMIT 10";

$resultado = $conexion->query($query);

$pedidos = [];

while ($row = $resultado->fetch_assoc()) {
    $pedidos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($pedidos);
?>
