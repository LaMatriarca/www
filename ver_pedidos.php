<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['success' => false, 'error' => 'No has iniciado sesión']);
    exit;
}

$clienteId = (int)$_SESSION['cliente_id'];

// Traer pedidos del cliente (asegúrate de tener columna id_cliente en pedidos)
$sqlPedidos = "SELECT id, folio, total, forma_pago, hora_entrega, fecha FROM pedidos WHERE id_cliente = $clienteId ORDER BY fecha DESC";

$resultPedidos = $mysqli->query($sqlPedidos);

$pedidos = [];

while ($pedido = $resultPedidos->fetch_assoc()) {
    $idPedido = $pedido['id'];
    $sqlDetalles = "SELECT nombre, cantidad, subtotal FROM detallespedidos WHERE id_pedido = $idPedido";
    $resultDetalles = $mysqli->query($sqlDetalles);

    $detalles = [];
    while ($detalle = $resultDetalles->fetch_assoc()) {
        $detalles[] = $detalle;
    }

    $pedido['detalles'] = $detalles;
    $pedidos[] = $pedido;
}

echo json_encode(['success' => true, 'pedidos' => $pedidos]);
?>
