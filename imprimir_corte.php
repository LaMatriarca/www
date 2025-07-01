<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'conexion.php';
require 'fpdf/fpdf.php';
require 'helpers/NumeroALetras.php';

define('MONEDA', '$');
define('MONEDA_LETRA', 'PESOS');
define('MONEDA_DECIMAL', 'CENTAVOS');

// Obtener el ID del corte desde GET (por ejemplo: imprimir_corte_completo.php?id=5)
$corte_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($corte_id <= 0) {
    die("ID de corte inválido");
}

// Obtener datos del corte
$sqlCorte = "SELECT * FROM corte_caja WHERE id = ?";
$stmtCorte = $mysqli->prepare($sqlCorte);
$stmtCorte->bind_param("i", $corte_id);
$stmtCorte->execute();
$resultCorte = $stmtCorte->get_result();
if ($resultCorte->num_rows === 0) {
    die("Corte no encontrado");
}
$corte = $resultCorte->fetch_assoc();

// Obtener ventas del turno según apertura_id del corte
$apertura_id = $corte['apertura_id'];
if (!$apertura_id) {
    die("El corte no tiene un apertura_id válido");
}

$sqlVentas = "SELECT v.id, v.folio, v.total, v.fecha, 
              GROUP_CONCAT(CONCAT(dv.cantidad, ' x ', dv.nombre) SEPARATOR ', ') AS productos
              FROM ventas v
              LEFT JOIN detallesventa dv ON dv.id_venta = v.id
              WHERE v.apertura_id = ?
              GROUP BY v.id
              ORDER BY v.fecha ASC";
$stmtVentas = $mysqli->prepare($sqlVentas);
$stmtVentas->bind_param("i", $apertura_id);
$stmtVentas->execute();
$resultVentas = $stmtVentas->get_result();

// Crear PDF con tamaño 80mm ancho
$pdf = new FPDF('P', 'mm', array(80, 250));
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5);

// Logo
$pdf->Image('images/logonv.png', 15, 2, 45);
$pdf->Ln(28);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'CORTE DE CAJA', 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Fecha corte: ' . date('d/m/Y', strtotime($corte['fecha'])), 0, 1, 'L');
$pdf->Cell(0, 5, 'Hora corte: ' . date('H:i:s', strtotime($corte['creado_en'])), 0, 1, 'L');
$pdf->Cell(0, 5, 'Apertura ID: ' . $apertura_id, 0, 1, 'L');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(27, 6, 'Apertura', 1, 0, 'C');
$pdf->Cell(27, 6, 'Ventas', 1, 0, 'C');
$pdf->Cell(26, 6, 'Cierre', 1, 1, 'C');

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(27, 6, MONEDA . ' ' . number_format($corte['apertura'], 2), 1, 0, 'R');
$pdf->Cell(27, 6, MONEDA . ' ' . number_format($corte['ventas'], 2), 1, 0, 'R');
$pdf->Cell(26, 6, MONEDA . ' ' . number_format($corte['cierre'], 2), 1, 1, 'R');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, 'VENTAS DEL TURNO', 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(20, 6, 'Folio', 1, 0, 'C');
$pdf->Cell(35, 6, 'Productos', 1, 0, 'C');
$pdf->Cell(25, 6, 'Total', 1, 1, 'C');

$totalVentasTurno = 0;
while ($venta = $resultVentas->fetch_assoc()) {
    $totalVentasTurno += $venta['total'];

    // Limitar texto de productos para que no desborde
    $productos = utf8_decode($venta['productos']);
    if (strlen($productos) > 45) {
        $productos = substr($productos, 0, 42) . '...';
    }

    $pdf->Cell(20, 6, $venta['folio'], 1, 0, 'C');
    $pdf->Cell(35, 6, $productos, 1, 0, 'L');
    $pdf->Cell(25, 6, MONEDA . ' ' . number_format($venta['total'], 2), 1, 1, 'R');
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, 'Total Ventas Turno: ' . MONEDA . ' ' . number_format($totalVentasTurno, 2), 0, 1, 'R');

$pdf->Ln(5);
$diferencia = $corte['diferencia'];
$resultado = ($diferencia == 0) ? 'CUADRA' : 'NO CUADRA';

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Diferencia: ' . MONEDA . ' ' . number_format($diferencia, 2), 0, 1, 'R');
$pdf->Cell(0, 6, 'Resultado: ' . $resultado, 0, 1, 'R');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, utf8_decode('¡GRACIAS POR SU PREFERENCIA!'), 0, 1, 'C');
$pdf->Cell(0, 6, utf8_decode('ARTEPAN CDP'), 0, 1, 'C');

$pdf->Output();
