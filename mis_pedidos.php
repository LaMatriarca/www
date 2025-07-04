<?php
session_start();
if (!isset($_SESSION['cliente_nombre'])) {
    header("Location: login.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "artepan");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$cliente = $_SESSION['cliente_nombre'];

// Obtener pedidos del cliente por nombre
$pedidos = $conexion->query("
    SELECT p.id, p.folio, p.total, p.forma_pago, p.hora_entrega, p.fecha
    FROM pedidos p
    INNER JOIN clientes c ON c.nombre = '$cliente'
    ORDER BY p.fecha DESC
");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Mis Pedidos</h1>
    <a href="index.php">← Volver al inicio</a>
    <div class="pedidos-lista">
        <?php while ($row = $pedidos->fetch_assoc()): ?>
            <div class="pedido">
                <h3>Pedido #<?php echo $row['folio']; ?></h3>
                <p><strong>Fecha:</strong> <?php echo $row['fecha']; ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($row['total'], 2); ?></p>
                <p><strong>Forma de pago:</strong> <?php echo ucfirst($row['forma_pago']); ?></p>
                <p><strong>Hora entrega:</strong> <?php echo $row['hora_entrega']; ?></p>

                <!-- Mostrar productos -->
                <ul>
                <?php
                    $idPedido = $row['id'];
                    $detalles = $conexion->query("SELECT nombre, cantidad, subtotal FROM detallespedidos WHERE id_pedido = $idPedido");
                    while ($prod = $detalles->fetch_assoc()):
                ?>
                    <li><?php echo $prod['cantidad'] . " x " . $prod['nombre'] . " ($" . $prod['subtotal'] . ")"; ?></li>
                <?php endwhile; ?>
                </ul>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
