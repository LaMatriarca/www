<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$nombre = trim($input['nombre'] ?? '');
$telefono = trim($input['telefono'] ?? '');
$correo = trim($input['correo'] ?? '');
$contrasena = $input['password'] ?? '';

// Validación básica
if (!$nombre || !$telefono || !$correo || !$contrasena) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/', $nombre)) {
    echo json_encode(['success' => false, 'message' => 'Nombre inválido.']);
    exit;
}
if (!preg_match('/^\d{10}$/', $telefono)) {
    echo json_encode(['success' => false, 'message' => 'Teléfono inválido.']);
    exit;
}
if (strlen($contrasena) < 8) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
    exit;
}

$con = new mysqli("localhost", "root", "", "artepan");
if ($con->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

// Validar si ya existe
$stmtCheck = $con->prepare("SELECT id FROM clientes WHERE correo = ?");
$stmtCheck->bind_param("s", $correo);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Este correo ya está registrado.']);
    $stmtCheck->close();
    $con->close();
    exit;
}

// Guardar
$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $con->prepare("INSERT INTO clientes (nombre, telefono, correo, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $telefono, $correo, $hash);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cliente registrado exitosamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar: ' . $con->error]);
}

$stmt->close();
$con->close();
?>
