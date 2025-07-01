<?php
$con = new mysqli("localhost", "root", "", "artepan");

// Validación de conexión
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

// Recoger datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['password'] ?? ''; // desde el input "password" del formulario

// Validaciones básicas
if (!$nombre || !$telefono || !$correo || !$contrasena) {
    die("Todos los campos son obligatorios.");
}

// Verificar si ya existe ese correo o nombre
$stmtCheck = $con->prepare("SELECT id FROM clientes WHERE correo = ? OR nombre = ?");
$stmtCheck->bind_param("ss", $correo, $nombre);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
if ($resultCheck->num_rows > 0) {
    die("El nombre o correo ya están registrados.");
}

// Encriptar contraseña
$hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

// Insertar en la base de datos
$stmt = $con->prepare("INSERT INTO clientes (nombre, telefono, correo, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $telefono, $correo, $hashContrasena);

if ($stmt->execute()) {
    echo "Cliente registrado correctamente.";
    // También podrías redirigir:
    header("Location: index.html");
} else {
    echo "Error al registrar cliente: " . $con->error;
}

$stmt->close();
$con->close();
?>
