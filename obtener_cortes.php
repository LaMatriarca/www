<?php
require 'conexion.php';

// Obtener parámetros de fechas
$fecha_inicio = isset($_GET['fecha_inicio']) ? $mysqli->real_escape_string($_GET['fecha_inicio']) : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $mysqli->real_escape_string($_GET['fecha_fin']) : null;

// Construir consulta base
$sql = "SELECT id, fecha, apertura, ventas, cierre, diferencia FROM corte_caja";

// Añadir filtro de fechas si existen ambos parámetros
if ($fecha_inicio && $fecha_fin) {
    $sql .= " WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$sql .= " ORDER BY id DESC LIMIT 50";

$result = $mysqli->query($sql);

$cortes = array(); // Inicializa $cortes como un array vacío

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cortes[] = array(
            'id' => $row['id'],
            'fecha' => $row['fecha'],
            'apertura' => (float)$row['apertura'],
            'ventas' => (float)$row['ventas'],
            'cierre' => (float)$row['cierre'],
            'diferencia' => (float)$row['diferencia']
        );
    }
}

header('Content-Type: application/json');
echo json_encode($cortes);
?>
