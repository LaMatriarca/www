<?php
session_start();
$con = new mysqli("localhost", "root", "", "artepan");

if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

if (!isset($_SESSION['empleado_id'])) {
    die("Error: No has iniciado sesión.");
}

// Validar que el monto venga y sea numérico
if (!isset($_POST['monto_apertura']) || !is_numeric($_POST['monto_apertura'])) {
    die("Error: Monto de apertura inválido.");
}

$empleado_id = $_SESSION['empleado_id'];
$monto = floatval($_POST['monto_apertura']);
$fecha = date('Y-m-d H:i:s');

$stmt = $con->prepare("INSERT INTO aperturas_caja (empleado_id, fecha_apertura, monto_apertura) VALUES (?, ?, ?)");
$stmt->bind_param("isd", $empleado_id, $fecha, $monto);

if ($stmt->execute()) {
    // Recuperar id de la apertura para sesión
    $_SESSION['apertura_id'] = $stmt->insert_id;
    // Redirigir sin imprimir nada antes
    header("Location: puntodeventa.php");
    exit();
} else {
    die("Error al abrir caja: " . $stmt->error);
}

$stmt->close();
$con->close();
?>
