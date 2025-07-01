<?php
require 'conexion.php'; // Aquí debe estar tu conexión mysqli con variable $conn o $mysqli
$conn = new mysqli("localhost", "root", "", "artepan");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['apertura_id'])) {
    // Si no hay apertura activa, regresa al inicio
    header("Location: abrir_caja.php");
    exit();
}

$consulta = "SELECT * FROM productos";
$resultado = $conn->query($consulta);

$productos = [];

if ($resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUNTO DE VENTA</title>
    <link rel="stylesheet" href="estiloo.css">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
  <aside class="barra-lateral">
    <h1 class="logo-artepan">
      ARTEPAN
      <img src="images/logonv.png" alt="">
    </h1>

    <nav class="menu-lateral">
      <button onclick="abrirModal()">
        <i class="fa-solid fa-cash-register"></i> Cerrar Caja
      </button>
      <button id="btn-salir">
        <i class="fa-solid fa-right-from-bracket" ></i> Salir
      </button>
    </nav>
  </aside>

  <div class="encabezado">
    <h1>ARTEPAN</h1>

    <div class="contenedor-principal">
      <!-- Catálogo -->
      <section class="catalogo">
        <h2>CATÁLOGO</h2>
        <div class="categorias">
          <button class="btn-categoria active" data-categoria="todos">Todos</button>
          <button class="btn-categoria" data-categoria="salados">Salados</button>
          <button class="btn-categoria" data-categoria="dulces">Dulces</button>
        </div>

        <div id="productos" class="catalogo-productos">
          <?php foreach ($productos as $producto): ?>
            <div class="producto" 
     data-categoria="<?= $producto['categoria'] ?>" 
     onclick="agregarAlCarrito(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre'], ENT_QUOTES) ?>', <?= $producto['precio'] ?>)">
              <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
              <p>$<?= number_format($producto['precio'], 2) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Carrito -->
      <aside class="carrito">
        <h2>CARRITO</h2>
        <p>Total: <span id="total">$0.00</span></p>
        <div id="items-carrito"></div>
        <button type="button" id="generar-ticket">Generar Ticket</button>
        <button id="vaciar-carrito" class="vaciar-btn">Vaciar Carrito</button>
      </aside>
    </div>
  </div>

    
  
    <!-- Modal para el Ticket -->
    <div id="ticket-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>ARTEPAN - TICKET</h2>
            <div id="ticket-detalle"></div>
            
            <!-- AÑADE ESTO DONDE VAN LOS TOTALES (reemplaza o añade dentro de ticket-total) -->
            <div class="ticket-total">
                <p>TOTAL: <span id="ticket-total">$0.00</span></p>
                
                <!-- 👇 NUEVO BLOQUE PARA PAGO RECIBIDO (PEGA ESTO) 👇 -->
                <div class="pago-input">
                    <label>PAGO RECIBIDO: $</label>
                    <input type="number" id="pago-recibido" placeholder="0.00" step="0.01" min="0">
                    <button id="calcular-cambio">Calcular Cambio</button>
                </div>
                <!-- 👆 FIN DEL NUEVO BLOQUE 👆 -->
                
                <p>RECIBIDO: $<span id="monto-recibido">0.00</span></p>
                <p>CAMBIÓ: $<span id="ticket-cambio">0.00</span></p>
            </div>
            
            <button id="imprimir-ticket">Imprimir Ticket</button>
        </div>

</div>
<script>
function cerrarCaja() {
    if (confirm("¿Estás seguro de cerrar la caja?")) {
        window.location.href = "cerrar_caja.php"; // Ajusta si tienes otro archivo
    }
}


</script>
<!-- Botón para abrir el modal -->


<!-- Modal CIERREEEEEEEEEEEEEEEEEEEEEEEEEEEEE-->
<div id="modalCierre">
  <div>
    <h2>Cierre de Caja</h2>
    <form id="formCierre">
      <label for="cierre">Monto de Cierre:</label>
      <input id="cierre" type="number" step="0.01" name="cierre" required placeholder="Ej. 1500.00" />
      <button type="submit">Registrar Cierre</button>
      <button type="button" onclick="cerrarModal()">Cancelar</button>
    </form>
    <p id="mensajeCierre"></p>
<button id="btnContinuar" style="display:none; margin-top: 10px;">Continuar</button>
  </div>
</div>

<script>
  function abrirModal() {
    document.getElementById('modalCierre').style.display = 'block';
    document.getElementById('mensajeCierre').innerText = '';
  }

  function cerrarModal() {
    document.getElementById('modalCierre').style.display = 'none';
  }

  document.getElementById('formCierre').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('cerrar_caja.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    const msg = document.getElementById('mensajeCierre');
    const btnContinuar = document.getElementById('btnContinuar');

    const textoFormateado = data.mensaje.replace(/\n/g, '<br>');
    msg.innerHTML = textoFormateado;
    msg.style.color = data.status === 'success' ? '#4CAF50' : '#D32F2F';

    if (data.status === 'success') {
      // Mostrar el botón Continuar
      btnContinuar.style.display = 'inline-block';

      // Al hacer clic, redirigir a cortes.php
      btnContinuar.onclick = () => {
        window.location.href = 'cortes.php';
      };

      // Opcional: cerrar modal y ocultar botón si quieres
      // cerrarModal();
    } else {
      // Ocultar el botón si hay error
      btnContinuar.style.display = 'none';
    }
  })
  .catch(err => {
    alert("Error al registrar cierre.");
  });
});

</script>
<script src="script.js"></script>
</body>
</html>