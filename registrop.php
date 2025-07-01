<?php
session_start();
$con = new mysqli("localhost", "root", "", "artepan");
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $nombre = $_POST["nombre"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];
    $activo = 1; // por defecto activo

    // Verificar si el usuario ya existe
    $stmt = $con->prepare("SELECT id FROM empleados WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $mensaje = "El usuario ya existe.";
    } else {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO empleados (usuario, nombre, telefono, correo, contrasena, activo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $usuario, $nombre, $telefono, $correo, $hash, $activo);
        if ($stmt->execute()) {
            $mensaje = "Empleado registrado con éxito.";
        } else {
            $mensaje = "Error al registrar: " . $stmt->error;
        }
    }
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html>
<head><title>Registro Empleado</title></head>
<body>
<h2>Registro de Empleado</h2>
<form method="POST">
    Usuario: <input type="text" name="usuario" required><br>
    Nombre: <input type="text" name="nombre" required><br>
    Teléfono: <input type="text" name="telefono" required><br>
    Correo: <input type="email" name="correo" required><br>
    Contraseña: <input type="password" name="contrasena" required><br>
    <button type="submit">Registrar</button>
</form>
<p><?= $mensaje ?></p>
</body>
</html>
