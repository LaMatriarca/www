<?php
session_start();
$con = new mysqli("localhost", "root", "", "artepan");
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

$response = ["status" => "error", "mensaje" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cierre = floatval($_POST['cierre']);
    $hoy = date('Y-m-d');

    // Obtener la última apertura del día con fecha y monto
    $sqlApertura = "SELECT id, monto_apertura, fecha_apertura FROM aperturas_caja WHERE DATE(fecha_apertura) = CURDATE() ORDER BY fecha_apertura DESC LIMIT 1";
    $resultApertura = $con->query($sqlApertura);
    $apertura = 0;
    $fechaApertura = null;

    if ($row = $resultApertura->fetch_assoc()) {
        $apertura = floatval($row['monto_apertura']);
        $fechaApertura = $row['fecha_apertura'];
    } else {
        $response["mensaje"] = "❌ No se encontró apertura para hoy.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Sumar solo las ventas desde la última apertura
    $sqlVentas = "SELECT SUM(total) as total_ventas FROM ventas WHERE fecha >= ?";
    $stmt = $con->prepare($sqlVentas);
    $stmt->bind_param("s", $fechaApertura);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $ventas = floatval($resultado['total_ventas']);

    // Calcular diferencia
    $diferencia = $cierre - ($apertura + $ventas);

    // Insertar el corte de caja
    $sqlInsert = "INSERT INTO corte_caja (fecha, apertura, ventas, cierre, diferencia) VALUES (?, ?, ?, ?, ?)";
    $stmtInsert = $con->prepare($sqlInsert);
    $stmtInsert->bind_param("sdddd", $hoy, $apertura, $ventas, $cierre, $diferencia);

    if ($stmtInsert->execute()) {
        $response["status"] = "success";
        $response["mensaje"] = "✔️ Cierre registrado.\nApertura: $apertura\nVentas: $ventas\nDiferencia: " . number_format($diferencia, 2);
    } else {
        $response["mensaje"] = "❌ Error: " . $con->error;
    }

    $stmtInsert->close();
    $stmt->close();
}

$con->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
