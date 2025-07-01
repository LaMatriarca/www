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

$total = floatval($data['total']);
$productos = $data['productos'];
$fecha = date("Y-m-d H:i:s");
$folio = "F" . time();

$apertura_id = isset($_SESSION['apertura_id']) ? intval($_SESSION['apertura_id']) : null;
if (!$apertura_id) {
    echo json_encode(["status" => "error", "message" => "No hay apertura activa"]);
    exit;
}

$conn->begin_transaction();

try {
    // Insertar venta con apertura_id
    $stmtVenta = $conn->prepare("INSERT INTO ventas (folio, fecha, total, apertura_id) VALUES (?, ?, ?, ?)");
    if (!$stmtVenta) throw new Exception("Error en preparación de venta: " . $conn->error);
    $stmtVenta->bind_param("ssdi", $folio, $fecha, $total, $apertura_id);
    if (!$stmtVenta->execute()) throw new Exception("Error al guardar venta: " . $stmtVenta->error);
    $id_venta = $stmtVenta->insert_id;
    $stmtVenta->close();

    // Insertar detalles de venta
    $stmtDetalle = $conn->prepare("INSERT INTO detallesventa (id_venta, id_producto, nombre, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)");
    if (!$stmtDetalle) throw new Exception("Error en preparación de detalle: " . $conn->error);

    foreach ($productos as $p) {
        $id_producto = intval($p['id']);
        $nombre = $p['nombre'];
        $cantidad = intval($p['cantidad']);
        $subtotal = floatval($p['precio']) * $cantidad;

        $stmtDetalle->bind_param("iisid", $id_venta, $id_producto, $nombre, $cantidad, $subtotal);
        if (!$stmtDetalle->execute()) throw new Exception("Error al guardar detalle: " . $stmtDetalle->error);
    }
    $stmtDetalle->close();

    $conn->commit();

    echo json_encode(["status" => "ok", "venta_id" => $id_venta]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
