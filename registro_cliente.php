<?php
// registro_cliente.php
// Conexión a base de datos (ajusta parámetros)
$host = "localhost";
$user = "root";
$password = "";
$dbname = "artepan";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir datos POST
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

// Validaciones básicas
if (!$nombre || !$telefono || !$correo || !$password) {
    die("Todos los campos son obligatorios.");
}

// Verificar que el usuario o correo no exista ya
$sqlCheck = "SELECT id FROM clientes WHERE nombre = ? OR correo = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $nombre, $correo);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
if ($resultCheck->num_rows > 0) {
    die("El usuario o correo ya existe.");
}

// Insertar usuario
$sqlInsert = "INSERT INTO clientes (nombre, telefono, correo, password) VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("ssss", $nombre, $telefono, $correo, $hashPassword);
