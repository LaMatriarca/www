<?php
require 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['carrito']) || empty($data['forma_pago']) || empty($data['hora_entrega'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$carrito = $data['carrito'];
$formaPago = $mysqli->real_escape_string($data['forma_pago']);
$horaEntrega = $mysqli->real_escape_string($data['hora_entrega']);

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

        $mysqli->query("INSERT INTO detallespedido (id_pedido, nombre, cantidad, subtotal) VALUES ($idPedido, '$nombre', $cantidad, $subtotal)");
    }

    echo json_encode(['success' => true, 'id_pedido' => $idPedido]);
} else {
    echo json_encode(['success' => false, 'error' => $mysqli->error]);
}
