<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "artepan");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión"]);
    exit;
}

session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['total']) || !isset($data['productos'])) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

$total = $data['total'];
$productos = $data['productos'];
$fecha = date("Y-m-d H:i:s");
$folio = "F" . time();

$stmtVenta = $conn->prepare("INSERT INTO ventas (folio, fecha, total) VALUES (?, ?, ?)");
$stmtVenta->bind_param("ssd", $folio, $fecha, $total);
$stmtVenta->execute();

if ($stmtVenta->affected_rows <= 0) {
    echo json_encode(["status" => "error", "message" => "No se pudo guardar venta"]);
    exit;
}

$id_venta = $stmtVenta->insert_id;
$stmtVenta->close();

$stmtDetalle = $conn->prepare("INSERT INTO detallesventa (id_venta, id_producto, nombre, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)");

foreach ($productos as $p) {
    $id_producto = $p['id'];
    $nombre = $p['nombre'];
    $cantidad = $p['cantidad'];
    $subtotal = $p['precio'] * $cantidad;

    $stmtDetalle->bind_param("iisid", $id_venta, $id_producto, $nombre, $cantidad, $subtotal);
    $stmtDetalle->execute();
}

$stmtDetalle->close();

echo json_encode(["status" => "ok", "venta_id" => $id_venta]);
