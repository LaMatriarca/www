<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$con = new mysqli("localhost", "root", "", "artepan");
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $contrasena = $_POST["contrasena"];

    // Primero buscar en empleados
    $stmt = $con->prepare("SELECT id, nombre, contrasena, 'empleado' AS tipo FROM empleados WHERE nombre = ? AND activo = 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $empleado = $resultado->fetch_assoc();
        if (password_verify($contrasena, $empleado['contrasena'])) {
            $_SESSION['empleado_id'] = $empleado['id'];
            $_SESSION['empleado_nombre'] = $empleado['nombre'];
            header("Location: cortes.php");
            exit;
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        // Si no es empleado, buscar en clientes
        $stmt = $con->prepare("SELECT id, nombre, password, 'cliente' AS tipo FROM clientes WHERE nombre = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $cliente = $resultado->fetch_assoc();
            if (password_verify($contrasena, $cliente['password'])) {
                $_SESSION['cliente_id'] = $cliente['id'];
                $_SESSION['cliente_nombre'] = $cliente['nombre'];
                header("Location: index.php"); // Redirigir a página principal
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado o inactivo.";
        }
    }

    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="LOGIN/style.css">
</head>
<body class="login">
    <div class="container">
        <img src="login/img/logo.png" alt="Logo ARTEPAN" class="logo">
        <h2>Iniciar Sesión</h2>

        <?php if ($mensaje): ?>
            <p style="color:red;"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" id="loginForm">
            <label for="usuario">Usuario (Administrador o Usuario):</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
            <button type="button" onclick="togglePassword()">👁️</button>

            <div class="button-group">
                <button type="submit">Iniciar Sesión</button>
                <button type="button" id="btnRegresar">Regresar</button>
            </div>
        </form>

        <p id="rolMensaje" class="mensaje"></p>

        <div class="registro-links">
            <p>¿Deseas registrarte?</p>
            <button onclick="seleccionarRegistro('Usuario')">Registrar como Usuario</button>
        </div>
    </div>

    <script>
        // Función vacía para evitar mostrar mensaje
        function setRolMensaje(rol) {
            // No hace nada
        }

        const rol = localStorage.getItem('rolSeleccionado');
        if (rol) setRolMensaje(rol);

        function seleccionarRegistro(rol) {
            localStorage.setItem('registroRol', rol);
            window.location.href = 'register.html';
        }

        // Función para confirmar antes de regresar
        function confirmarRegresar(url) {
            if (confirm("¿Estás seguro que quieres regresar a la página principal?")) {
                window.location.href = 'index.php';
            }
        }

        document.getElementById('btnRegresar').addEventListener('click', function() {
            confirmarRegresar('menu.html');
        });

        function togglePassword() {
            const passInput = document.getElementById('contrasena');
            if (passInput.type === "password") {
                passInput.type = "text";
            } else {
                passInput.type = "password";
            }
        }
    </script>
</body>
</html>
