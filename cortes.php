<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header("Location: login.php?error=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Artepan - Panel Principal</title>
  <link rel="stylesheet" href="cortes.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    /* Estilos para los botones de navegación */
    .nav-buttons {
      margin: 10px 0;
    }
    .nav-buttons button {
      background-color: #007bff;
      border: none;
      color: white;
      padding: 8px 14px;
      margin-right: 10px;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background-color 0.3s ease;
    }
    .nav-buttons button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <header>
    <h1>Artepan - Panel Principal</h1>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['empleado_nombre']); ?>.</p>

    <div class="nav-buttons">
      <button onclick="window.location.href='index.php'">
        <i class="fas fa-home"></i> Inicio
      </button>
      <button onclick="history.back()">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>
  </header>
<div id="notificacion-pedidos" onclick="mostrarNotificaciones()" title="Pedidos nuevos">
  <i class="fas fa-bell"></i>
  <span id="contador-pedidos">0</span>
</div>

<div id="panel-pedidos" class="oculto">
  <h4>Pedidos Recientes</h4>
  <ul id="lista-pedidos"></ul>

  <button id="btn-whatsapp" type="button">
    <i class="fab fa-whatsapp"></i> Verificar pedidos en Whatsapp
  </button>
</div>
  <div class="main-container">
    <aside class="sidebar">
      <button onclick="mostrarSeccion('caja')">
        <i class="fas fa-cash-register"></i> Caja
      </button>
      <button onclick="mostrarSeccion('reportes')">
        <i class="fas fa-chart-line"></i> Reportes
      </button>
      
      <button onclick="irInventario()">
    <i class="fa-solid fa-boxes-stacked"></i> Inventario
  </button>
    </aside>

    <main>
      <section id="caja" class="contenido">
        <h2>Sección de Caja</h2>
        <form id="form-apertura" method="POST" action="abrir_caja.php">
          <label for="monto">Monto de apertura:</label>
          <input type="number" step="0.01" name="monto_apertura" id="monto" required />
          <button type="submit">Abrir Caja</button>
        </form>
      </section>

      <section id="reportes" class="contenido oculto">
        <h2>Reportes</h2>
        <p>Consulta informes de ventas, productos y clientes.</p>
        <form id="form-busqueda" method="GET">
          <h3>Buscar reportes desde: 
            <input type="date" name="fecha_inicio" id="fecha-inicio" required>
          Hasta:
            <input type="date" name="fecha_fin" id="fecha-fin" required>
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
            <div class="loader-dots" id="loader" aria-label="Cargando" role="status" aria-live="polite" style="display:none;">
      <span></span><span></span><span></span>
          </h3>
        </form>
        <div id="busqueda"></div>

        <div class="tabla-reportes">
          <h3 style="margin-top:1.5em;">Cierres de Caja Recientes</h3>
          <table>
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Apertura</th>
                <th>Ventas</th>
                <th>Cierre</th>
                <th>Diferencia</th>
                <th>Ticket</th>
              </tr>
            </thead>
            <tbody id="tabla-cortes">
            </tbody>
          </table>
        </div>
        <button type="button" onclick="imprimirSuperTicket()" class="super-ticket-button">
    <i class="fas fa-print" ></i> Imprimir Reporte General
</button>
      </section>

      <section id="cierre" class="contenido oculto">
        <h2>Cierre de Caja</h2>
        <p>Realiza el resumen diario y conciliación de caja.</p>
      </section>
    </main>
  </div>
  <!-- Botón de ayuda flotante -->
<button id="btn-ayuda" class="btn-ayuda-flotante" title="Ayuda">
  <i class="fas fa-question-circle"></i>
</button>

<!-- Panel ayuda -->
<div id="panel-ayuda" class="panel-ayuda oculto">
  <h3>Ayuda</h3>
  <p>
    Bienvenido al panel principal de Artepan.<br>
    Usa los botones de navegación para moverte entre secciones.<br>
    En "Caja" puedes registrar la apertura.<br>
    En "Reportes" consulta cierres y ventas.<br><br>
    Para más detalles, consulta:<br>
    <a href="manual_usuario.pdf" target="_blank" rel="noopener noreferrer">Manual de Usuario</a><br>
    <a href="guia_rapida.pdf" target="_blank" rel="noopener noreferrer">Guía Rápida</a><br><br>
    Si necesitas más ayuda, contacta al administrador.
  </p>
</div>

<script>
 let intervaloCortes;

function mostrarSeccion(id) {
  const secciones = document.querySelectorAll('.contenido');
  secciones.forEach(sec => sec.classList.add('oculto'));
  document.getElementById(id).classList.remove('oculto');

  if (id === 'reportes') {
    cargarCortes();
    intervaloCortes = setInterval(cargarCortes, 5000);
  } else {
    clearInterval(intervaloCortes);
  }
}

function irInventario() {
  window.location.href = 'inventario.html';
}

function imprimirSuperTicket() {
  const fechaInicio = document.getElementById('fecha-inicio').value;
  const fechaFin = document.getElementById('fecha-fin').value;

  if (!fechaInicio || !fechaFin) {
    alert("Por favor, selecciona ambas fechas para imprimir.");
    return;
  }

  const url = `obtener_cortes.php?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (data.length === 0) {
        alert("No hay cortes para imprimir en ese rango de fechas.");
        return;
      }

      let totalVentas = 0;
      let totalPerdidas = 0;

      let contenido = `
        <html>
        <head>
          <title>Corte de caja general - Artepan</title>
          <style>
            body { font-family: monospace; padding: 10px; }
            h2 { text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #000; padding: 5px; text-align: center; }
            th { background-color: #eee; }
            tfoot td { font-weight: bold; }
          </style>
        </head>
        <body>
          <h2> - Corte de Caja -</h2>
          <p>Desde: ${fechaInicio} Hasta: ${fechaFin}</p>
          <table>
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Apertura</th>
                <th>Ventas</th>
                <th>Cierre</th>
                <th>Diferencia</th>
              </tr>
            </thead>
            <tbody>
      `;

      data.forEach(corte => {
        totalVentas += parseFloat(corte.ventas);
        if (parseFloat(corte.diferencia) < 0) {
          totalPerdidas += parseFloat(corte.diferencia);
        }

        contenido += `
          <tr>
            <td>${corte.fecha}</td>
            <td>$${parseFloat(corte.apertura).toFixed(2)}</td>
            <td>$${parseFloat(corte.ventas).toFixed(2)}</td>
            <td>$${parseFloat(corte.cierre).toFixed(2)}</td>
            <td style="color: ${corte.diferencia < 0 ? '#c0392b' : '#27ae60'};">
              $${parseFloat(corte.diferencia).toFixed(2)}
            </td>
          </tr>
        `;
      });

      contenido += `
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2">Totales</td>
                <td>$${totalVentas.toFixed(2)}</td>
                <td></td>
                <td style="color: #c0392b;">$${totalPerdidas.toFixed(2)}</td>
              </tr>
            </tfoot>
          </table>
        </body>
        </html>
      `;

      const ventanaImpresion = window.open('', '_blank', 'width=600,height=800');
      ventanaImpresion.document.write(contenido);
      ventanaImpresion.document.close();
      ventanaImpresion.focus();

      ventanaImpresion.onload = function () {
        ventanaImpresion.print();
      };
    })
    .catch(error => {
      console.error('Error al obtener datos para imprimir:', error);
      alert('Error al obtener datos para imprimir.');
    });
}

function cargarCortes() {
  const fechaInicio = document.getElementById('fecha-inicio').value;
  const fechaFin = document.getElementById('fecha-fin').value;
  const loader = document.getElementById('loader');
  const tbody = document.getElementById('tabla-cortes');

  let url = 'obtener_cortes.php';

  if (fechaInicio && fechaFin) {
    url += `?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
  }

  loader.style.display = 'inline-flex';
  tbody.innerHTML = '';

  fetch(url)
    .then(response => response.json())
    .then(data => {
      loader.style.display = 'none';

      if (data.length === 0) {
        tbody.innerHTML = "<tr><td colspan='6'>No hay cierres registrados.</td></tr>";
        return;
      }

      data.forEach(corte => {
        const color = corte.diferencia < 0 ? "#c0392b" : "#27ae60";
        const fila = `
          <tr>
            <td>${corte.fecha}</td>
            <td>$${parseFloat(corte.apertura).toFixed(2)}</td>
            <td>$${parseFloat(corte.ventas).toFixed(2)}</td>
            <td>$${parseFloat(corte.cierre).toFixed(2)}</td>
            <td style="color: ${color};">$${parseFloat(corte.diferencia).toFixed(2)}</td>
            <td>
              <form method="GET" action="imprimir_corte.php" target="_blank">
                <input type="hidden" name="id" value="${corte.id}">
                <button type='submit'><i class='fas fa-print'></i>Imprimir</button>
              </form>
            </td>
          </tr>
        `;
        tbody.innerHTML += fila;
      });
    })
    .catch(error => {
      console.error('Error al cargar cortes:', error);
      loader.style.display = 'none';
      tbody.innerHTML = "<tr><td colspan='6'>Error al cargar los datos.</td></tr>";
    });
}

// 🔔 Notificaciones
function mostrarNotificaciones() {
  const panel = document.getElementById('panel-pedidos');
  panel.classList.toggle('oculto');
}

function cargarPedidosRecientes() {
  fetch('obtener_pedidos_recientes.php')
    .then(response => response.json())
    .then(data => {
      const contador = document.getElementById('contador-pedidos');
      const lista = document.getElementById('lista-pedidos');
      lista.innerHTML = '';

      if (data.length === 0) {
        contador.textContent = '0';
        document.getElementById('notificacion-pedidos').style.display = 'none';
        return;
      }

      document.getElementById('notificacion-pedidos').style.display = 'flex';
      contador.textContent = data.length;

      data.forEach(pedido => {
        const item = document.createElement('li');
        item.innerHTML = `<strong>${pedido.folio}</strong> - $${parseFloat(pedido.total).toFixed(2)}<br><small>${pedido.hora_entrega}</small>`;
        lista.appendChild(item);
      });
    })
    .catch(err => {
      console.error('Error al cargar pedidos:', err);
    });
}

// Botón WhatsApp para verificar pedidos
function abrirWhatsApp() {
  const numeroWhatsApp = '5215628419958';
  const mensaje = encodeURIComponent('Hola, quiero verificar los pedidos recientes.');
  const url = `https://wa.me/${numeroWhatsApp}?text=${mensaje}`;
  window.open(url, '_blank');
}

// Función para mostrar el panel de ayuda flotante
function mostrarPanelAyuda() {
  const panel = document.getElementById('panel-ayuda');
  if (panel) {
    if (panel.classList.contains('visible')) {
      panel.classList.remove('visible');
      panel.classList.add('oculto');
    } else {
      panel.classList.remove('oculto');
      panel.classList.add('visible');
    }
  }
}
// Inicialización
document.addEventListener('DOMContentLoaded', () => {
  const formBusqueda = document.getElementById('form-busqueda');
  formBusqueda.addEventListener('submit', function (event) {
    event.preventDefault();
    cargarCortes();
  });

  mostrarSeccion('caja');
  cargarPedidosRecientes();
  setInterval(cargarPedidosRecientes, 5000);

  // Aquí agregamos el listener para el botón WhatsApp
  const btnWhatsApp = document.getElementById('btn-whatsapp');
  if (btnWhatsApp) {
    btnWhatsApp.addEventListener('click', abrirWhatsApp);
  }

  // Listener para el botón de ayuda flotante
  const btnAyuda = document.getElementById('btn-ayuda');
  if (btnAyuda) {
    btnAyuda.addEventListener('click', mostrarPanelAyuda);
  }
});

</script>

</body>
</html>
