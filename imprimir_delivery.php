<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'conexion.php';
require 'fpdf/fpdf.php';
require 'helpers/NumeroALetras.php';

define('MONEDA', '$');
define('MONEDA_LETRA', 'PESOS');
define('MONEDA_DECIMAL', 'CENTAVOS');

$idPedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del pedido
$sqlPedido = "SELECT folio, total, forma_pago, hora_entrega, 
              DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha, 
              DATE_FORMAT(fecha, '%H:%i') AS hora 
              FROM pedidos WHERE id = $idPedido LIMIT 1";
$resPedido = $mysqli->query($sqlPedido);
$pedido = $resPedido->fetch_assoc();

if (!$pedido) {
    die("Pedido no encontrado.");
}

// Obtener detalles
$sqlDetalles = "SELECT nombre, cantidad, subtotal FROM detallespedidos WHERE id_pedido = $idPedido";
$resDetalles = $mysqli->query($sqlDetalles);

// Crear PDF
$pdf = new FPDF('P', 'mm', array(80, 220));
$pdf->AddPage();
$pdf->SetMargins(5,5,5);
$pdf->SetFont('Arial', 'B', 9);

// Logo
$pdf->Image('images/logonv.png', 15, 2, 45);
$pdf->Ln(28);

// Encabezado
$pdf->Cell(70, 5, 'PEDIDO - ARTEPAN CDP', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(20, 5, 'Folio:', 0, 0);
$pdf->Cell(50, 5, $pedido['folio'], 0, 1);

$pdf->Cell(20, 5, 'Fecha:', 0, 0);
$pdf->Cell(50, 5, $pedido['fecha'] . ' ' . $pedido['hora'], 0, 1);

$pdf->Cell(20, 5, 'Entrega:', 0, 0);
$pdf->Cell(50, 5, $pedido['hora_entrega'], 0, 1);

$pdf->Cell(70, 2, '--------------------------------------------', 0, 1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(10, 5, 'Cant', 0, 0);
$pdf->Cell(35, 5, 'Producto', 0, 0);
$pdf->Cell(25, 5, 'Importe', 0, 1);

$pdf->Cell(70, 2, '--------------------------------------------', 0, 1);

$pdf->SetFont('Arial', '', 8);
$totalCantidad = 0;
while ($row = $resDetalles->fetch_assoc()) {
    $importe = number_format($row['subtotal'], 2);
    $pdf->Cell(10, 5, $row['cantidad'], 0, 0);
    $pdf->Cell(35, 5, mb_convert_encoding($row['nombre'], 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(25, 5, MONEDA . ' ' . $importe, 0, 1);
    $totalCantidad += $row['cantidad'];
}

$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, 'TOTAL: ' . MONEDA . number_format($pedido['total'], 2), 0, 1, 'R', 'MXN');

$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(70, 5, 'SON: ' . NumeroALetras::convertir($pedido['total'], MONEDA_LETRA, MONEDA_DECIMAL));

$pdf->Ln(2);

// Instrucciones de pago
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, 'Forma de pago: ' . strtoupper($pedido['forma_pago']), 0, 1);

$pdf->SetFont('Arial', '', 7);
if ($pedido['forma_pago'] == 'transferencia') {
    $pdf->MultiCell(70, 4, "Por favor realiza la transferencia a:\nBANCO: BANAMEX\nCUENTA: 1234567890\nCLABE: 012345678901234567\nUna vez hecho, envía tu comprobante al WhatsApp 555-123-4567.");
} else {
    $pdf->MultiCell(70, 4, "Por favor paga en efectivo al momento de recoger tu pedido.");
}

$pdf->Ln(4);
$pdf->Cell(70, 5, 'Gracias por tu pedido en ARTEPAN CDP', 0, 1, 'C');
$pdf->Cell(70, 5, 'Te esperamos a la hora indicada.', 0, 1, 'C');

$pdf->Output();
