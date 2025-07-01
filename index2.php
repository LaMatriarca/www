<?php
session_start();

// Verificamos si hay sesión iniciada
if (!isset($_SESSION['empleado_id'])) {
    header("Location: login.php");
    exit;
}



require 'conexion.php';
require 'fpdf/fpdf.php';
require 'helpers/NumeroALetras.php';


define('MONEDA', '$');
define('MONEDA_LETRA', 'PESOS');
define('MONEDA_DECIMAL', 'CENTAVOS');

$fechaHoy = date('Y-m-d');


// Obtener todas las ventas del día
$sqlVentas = "SELECT id, folio, total, DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha_venta, DATE_FORMAT(fecha, '%H:%i') AS hora 
              FROM ventas 
              WHERE DATE(fecha) = '$fechaHoy' 
              ORDER BY fecha ASC";
$resultadoVentas = $mysqli->query($sqlVentas);

// Inicializar acumuladores
$totalDia = 0;
$totalProductos = 0;
$gananciaTotal = 0;

$pdf = new FPDF('P', 'mm', array(80, 200 + $resultadoVentas->num_rows * 10));
$pdf->AddPage();
$pdf->SetMargins(5,5,5);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Image('images/logonv.png', 15, 2, 45);
$pdf->Ln(28); 
$pdf->Cell(70, 5, 'ARTEPAN CDP', 0, 1, 'C');
$pdf->Ln(1); 
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, 'Cajero: ' . mb_convert_encoding($_SESSION['empleado_nombre'], 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(5); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'REPORTE DE VENTAS DEL '.date('d/m/Y', strtotime($fechaHoy)), 0, 1, 'C');
$pdf->Cell(70, 2, str_repeat('-', 70), 0, 1, 'L');

while ($venta = $resultadoVentas->fetch_assoc()) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 5, 'Folio:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(50, 5, $venta['folio'] . ' | ' . $venta['hora'], 0, 1, 'L');

    $idVenta = $venta['id'];
    $sqlDetalle = "SELECT dv.nombre, dv.cantidad, dv.subtotal, p.precio AS precio_producto
                   FROM detallesventa dv
                   JOIN productos p ON dv.id_producto = p.id
                   WHERE dv.id_venta = $idVenta";
    $resultadoDetalle = $mysqli->query($sqlDetalle);

    $pdf->SetFont('Arial', '', 7);
    while ($row = $resultadoDetalle->fetch_assoc()) {
        $importe = $row['subtotal'];
        $costo = $row['precio_producto'] * $row['cantidad'];
        $ganancia = $importe - $costo;

        $nombre_producto = mb_convert_encoding($row['nombre'], 'ISO-8859-1', 'UTF-8');

        $pdf->Cell(10, 4, $row['cantidad'],0,0,'L');
        $pdf->Cell(30, 4, $nombre_producto, 0, 0, 'L');
        $pdf->Cell(15, 4, MONEDA . ' ' . number_format($row['subtotal'], 2), 0, 0, 'L');
        $pdf->Cell(15, 4, MONEDA . ' ' . number_format($importe, 2), 0, 1, 'L');

        $totalProductos += $row['cantidad'];
        $gananciaTotal += $ganancia;
    }

    $totalDia += $venta['total'];
    $pdf->Cell(70, 2, str_repeat('-', 70), 0, 1, 'L');
}

$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, 'RESUMEN DEL DIA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 4, 'Total de productos vendidos: ' . $totalProductos, 0, 1, 'L');
$pdf->Cell(70, 4, 'Total vendido: ' . MONEDA . ' ' . number_format($totalDia, 2), 0, 1, 'L');
$pdf->Cell(70, 4, mb_convert_encoding('Ganancias del día: ' ,'ISO-8859-1', 'UTF-8'). MONEDA . ' ' . number_format($gananciaTotal, 2), 0, 1, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 7);

$pdf->Cell(70, 4, 'SON ' . NumeroALetras::convertir($totalDia, MONEDA_LETRA, MONEDA_DECIMAL), 0, 'L');

$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, '¡GRACIAS POR SU TRABAJO HOY!', 0, 1, 'C');

$pdf->Output();
