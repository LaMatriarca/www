<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['success' => false, 'error' => 'No has iniciado sesión']);
    exit;
}

if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['carrito']) || empty($data['forma_pago']) || empty($data['hora_entrega'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$clienteId = (int) $_SESSION['cliente_id'];
$carrito = $data['carrito'];
$formaPago = $mysqli->real_escape_string($data['forma_pago']);
$horaEntrega = $mysqli->real_escape_string($data['hora_entrega']);

// Formatear hora a HH:MM:SS si es necesario
if (strlen($horaEntrega) <= 5) {
    $horaEntrega = date('H:i:s', strtotime($horaEntrega));
}

$total = 0;
foreach ($carrito as $item) {
    $total += $item['subtotal'];
}

$folio = uniqid('PED-');

// Insertar pedido con id_cliente
$sql = "INSERT INTO pedidos (folio, total, forma_pago, hora_entrega, id_cliente) VALUES ('$folio', $total, '$formaPago', '$horaEntrega', $clienteId)";
if ($mysqli->query($sql)) {
    $idPedido = $mysqli->insert_id;

    foreach ($carrito as $item) {
        $nombre = $mysqli->real_escape_string($item['nombre']);
        $cantidad = (int) $item['cantidad'];
        $subtotal = (float) $item['subtotal'];

        $result = $mysqli->query("INSERT INTO detallespedidos (id_pedido, nombre, cantidad, subtotal) VALUES ($idPedido, '$nombre', $cantidad, $subtotal)");
        if (!$result) {
            echo json_encode(['success' => false, 'error' => 'Error al guardar detalle: ' . $mysqli->error]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'id_pedido' => $idPedido]);
} else {
    echo json_encode(['success' => false, 'error' => $mysqli->error]);
}
