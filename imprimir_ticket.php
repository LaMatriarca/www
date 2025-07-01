<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'conexion.php';
require 'fpdf/fpdf.php';
require 'helpers/NumeroALetras.php';

define('MONEDA', '$');
define('MONEDA_LETRA', 'PESOS');
define('MONEDA_DECIMAL', 'CENTAVOS');

// Obtener y validar el ID de venta
$idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Consulta para obtener datos de la venta
$sqlVenta = "SELECT folio, total, DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha_venta, DATE_FORMAT(fecha, '%H:%i') AS hora  
             FROM ventas WHERE id = $idVenta LIMIT 1";
$resultado = $mysqli->query($sqlVenta);

if (!$resultado || $resultado->num_rows === 0) {
    die("Venta no encontrada");
}

$row_venta = $resultado->fetch_assoc();
$total = number_format($row_venta['total'], 2, '.', ',');

// Consulta para obtener detalles de la venta
$sqlDetalle = "SELECT nombre, cantidad, subtotal FROM detallesventa WHERE id_venta = $idVenta";
$resultadoDetalle = $mysqli->query($sqlDetalle);

$pdf = new FPDF('P', 'mm', array(80, 200));
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5);
$pdf->SetFont('Arial', 'B', 9);

// Logo
$pdf->Image('images/logonv.png', 15, 2, 45);
$pdf->Ln(28);

$pdf->Cell(70, 5, 'ARTEPAN CDP', 0, 1, 'C');
$pdf->Ln(7);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Folio ticket:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(15, 5, utf8_decode($row_venta['folio']), 0, 1, 'L');

$pdf->Cell(70, 2, '-------------------------------------------------------------------------', 0, 1, 'L');

$pdf->Cell(10, 4, 'Cant.', 0, 0, 'L');
$pdf->Cell(30, 4, utf8_decode('Descripción'), 0, 0, 'L');
$pdf->Cell(15, 4, 'Precio', 0, 0, 'L');
$pdf->Cell(15, 4, 'Importe', 0, 1, 'L');
$pdf->Cell(70, 2, '-------------------------------------------------------------------------', 0, 1, 'L');

$totalProductos = 0;
$pdf->SetFont('Arial', '', 7);

while ($row_producto = $resultadoDetalle->fetch_assoc()) {
    $precioUnitario = number_format($row_producto['subtotal'] / $row_producto['cantidad'], 2, '.', ',');
    $importe = number_format($row_producto['subtotal'], 2, '.', ',');
    $totalProductos += $row_producto['cantidad'];

    $pdf->Cell(10, 4, $row_producto['cantidad'], 0, 0, 'L');
    $pdf->Cell(30, 4, utf8_decode($row_producto['nombre']), 0, 0, 'L');
    $pdf->Cell(15, 4, MONEDA . ' ' . $precioUnitario, 0, 0, 'L');
    $pdf->Cell(15, 4, MONEDA . ' ' . $importe, 0, 1, 'L');
}

$pdf->Ln(2);
$pdf->Cell(70, 4, utf8_decode('Número de productos: ') . $totalProductos, 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 5, 'Total: ' . MONEDA . ' ' . $total, 0, 1, 'R');

$pdf->Ln(2);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 4, utf8_decode('SON ') . NumeroALetras::convertir($row_venta['total'], MONEDA_LETRA, MONEDA_DECIMAL), 0, 1, 'L');

$pdf->Ln();

$pdf->Cell(35, 5, 'Fecha: ' . $row_venta['fecha_venta'], 0, 0, 'C');
$pdf->Cell(35, 5, 'Hora: ' . $row_venta['hora'], 0, 1, 'C');

$pdf->Ln();

$pdf->Cell(70, 5, utf8_decode('AGRADECEMOS SU PREFERENCIA'), 0, 1, 'C');
$pdf->Cell(70, 5, utf8_decode('VUELVA PRONTO...'), 0, 1, 'C');

$pdf->Output();
