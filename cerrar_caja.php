<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['apertura_id'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'No hay apertura activa en sesión']);
    exit;
}

$monto_cierre = $_POST['cierre'] ?? null;

if ($monto_cierre === null || !is_numeric($monto_cierre)) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Monto de cierre inválido']);
    exit;
}

$apertura_id = $_SESSION['apertura_id'];

// Obtener monto de apertura
$stmt = $mysqli->prepare("SELECT monto_apertura FROM aperturas_caja WHERE id = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Error en consulta: ' . $mysqli->error]);
    exit;
}
$stmt->bind_param("i", $apertura_id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 0) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Apertura no encontrada']);
    exit;
}
$apertura = $resultado->fetch_assoc();

// Calcular ventas totales del turno actual
$ventas_total = calcularVentasTotales($mysqli, $apertura_id);

// Calcular diferencia
$diferencia = $monto_cierre - ($apertura['monto_apertura'] + $ventas_total);

// Insertar corte de caja con apertura_id
$stmt = $mysqli->prepare("INSERT INTO corte_caja (fecha, creado_en, apertura, ventas, cierre, diferencia, apertura_id) VALUES (CURDATE(), NOW(), ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Error en inserción: ' . $mysqli->error]);
    exit;
}
$stmt->bind_param("ddddi", $apertura['monto_apertura'], $ventas_total, $monto_cierre, $diferencia, $apertura_id);

if ($stmt->execute()) {
    $id_corte = $mysqli->insert_id;

    // Marcar apertura como cerrada (si tienes columna estado)
    $stmtCerrar = $mysqli->prepare("UPDATE aperturas_caja SET estado = 'cerrada' WHERE id = ?");
    if ($stmtCerrar) {
        $stmtCerrar->bind_param("i", $apertura_id);
        $stmtCerrar->execute();
        $stmtCerrar->close();
    }

    unset($_SESSION['apertura_id']);
    echo json_encode([
        'status' => 'success',
        'fecha' => date('Y-m-d'),
        'id_corte' => $id_corte, // <-- esto es nuevo
        'mensaje' => "Corte registrado:\nApertura: {$apertura['monto_apertura']}\nVentas: $ventas_total\nCierre: $monto_cierre\nDiferencia: $diferencia"
    ]);
} else {
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al registrar el corte: ' . $stmt->error]);
}

/**
 * Función para calcular el total de ventas del turno actual
 */
function calcularVentasTotales($mysqli, $apertura_id) {
    $sql = "SELECT SUM(total) as total FROM ventas WHERE apertura_id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return 0;
    $stmt->bind_param("i", $apertura_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}
