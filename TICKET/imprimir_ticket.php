<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'conexion.php';
require 'fpdf/fpdf.php';
require 'helpers/NumeroALetras.php';

define('MONEDA', '$');
define('MONEDA_LETRA', 'PESOS');
define('MONEDA_DECIMAL', 'CENTAVOS');

$idVenta = isset($_GET['id']) ? $mysqli->real_escape_string($_GET['id']) : 1;

$sqlVenta = "SELECT folio, total, DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha_venta,DATE_FORMAT(fecha, '%H:%i') AS hora  
 FROM ventas WHERE id = $idVenta LIMIT 1";
$resultado = $mysqli->query($sqlVenta);
$row_venta = $resultado->fetch_assoc();

$total = $row_venta['total'];

$sqlDetalle = "SELECT nombre, cantidad, subtotal FROM detallesventa WHERE id_venta = $idVenta";
$resultadoDetalle = $mysqli->query($sqlDetalle);

$pdf = new FPDF('P', 'mm', array(80,200));
$pdf->AddPage();
$pdf->SetMargins(5,5,5);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Image('images/logonv.png', 15, 2, 45);
$pdf->Ln(28); 

$pdf->Cell(70, 5, 'ARTEPAN CDP', 0, 1, 'C');
$pdf->Ln(7); 

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20,5, 'Folio ticket:', 0, 0, 'L');

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(15,5, $row_venta['folio'], 0, 1, 'L');

$pdf->Cell(70, 2, '-------------------------------------------------------------------------', 0, 1, 'L');

$pdf->Cell(10, 4, 'Cant.',0,0,'L');
$pdf->Cell(30, 4, mb_convert_encoding('Descripción', 'ISO-8859-1', 'UTF-8'),0,0,'L');
$pdf->Cell(15, 4, 'Precio',0,0,'L');
$pdf->Cell(15, 4, 'Importe',0,1,'L');
$pdf->Cell(70, 2, '-------------------------------------------------------------------------', 0, 1, 'L');

$totalProducto = 0;
$pdf->SetFont('Arial', '', 7);

while($row_producto = $resultadoDetalle->fetch_assoc()){
    $precioUnitario = number_format($row_producto['subtotal'] / $row_producto['cantidad'], 2, '.', ',');
    $importe = number_format($row_producto['subtotal'], 2, '.', ',');
    $totalProducto += $row_producto['cantidad'];

    $pdf->Cell(10, 4, $row_producto['cantidad'],0,0,'L');
    $nombre_producto = mb_convert_encoding($row_producto['nombre'], 'ISO-8859-1', 'UTF-8');
    $pdf->Cell(30, 4, $nombre_producto, 0, 0, 'L');
    $pdf->Cell(15, 4, MONEDA. ' '.$precioUnitario,0,0,'L');
    $pdf->Cell(15, 4, MONEDA. ' '.$importe,0,1,'L');
}
$pdf->Ln(2);
$pdf->Cell(70,4,mb_convert_encoding('Número de productos: ','ISO-8859-1', 'UTF-8') . $totalProducto, 0, 1, 'L'); 

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0,5, 'Total: '. $total, 0, 1, 'R');

$pdf->Ln(2);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70,4, 'SON '. NumeroALetras::convertir($total, MONEDA_LETRA, MONEDA_DECIMAL), 0,'L');

$pdf->Ln();

$pdf->Cell(35,5,'Fecha: '.$row_venta['fecha_venta'], 0, 0, 'C');
$pdf->Cell(35,5, 'Horas: '.$row_venta['hora'],0,1,'C');

$pdf->Ln();

$pdf->Cell(70, 5, 'AGRADECEMOS SU PREFERENCIA', 0, 1, 'C');
$pdf->Cell(70, 5, 'VUELVA PRONTO...', 0, 1, 'C');  
$pdf->Output();     
