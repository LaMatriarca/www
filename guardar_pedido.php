<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'conexion.php';
header('Content-Type: application/json');

if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['carrito']) || empty($data['forma_pago']) || empty($data['hora_entrega'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$carrito = $data['carrito'];
$formaPago = $mysqli->real_escape_string($data['forma_pago']);
$horaEntrega = $mysqli->real_escape_string($data['hora_entrega']);

// Para insertar hora correctamente, revisa que el formato sea compatible con tu campo en BD
// Por ejemplo, si es TIME, usa formato 'HH:MM:SS'
// Si solo tienes '8:00', mejor agrega segundos: '08:00:00'
if (strlen($horaEntrega) <= 5) {
    $horaEntrega = date('H:i:s', strtotime($horaEntrega));
}

$total = 0;
foreach ($carrito as $item) {
    $total += $item['subtotal'];
}

$folio = uniqid('PED-');

$sql = "INSERT INTO pedidos (folio, total, forma_pago, hora_entrega) VALUES ('$folio', $total, '$formaPago', '$horaEntrega')";
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
