<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header("Location: login.php");
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
      </tr>
    </thead>
    <tbody>
      <?php
      // Conexión a la base de datos (usa tu conexión actual)
      $conn = new mysqli("localhost", "root", "", "artepan");
      $sql = "SELECT fecha, apertura, ventas, cierre, diferencia FROM corte_caja ORDER BY fecha DESC LIMIT 10";
      $result = $conn->query($sql);
      if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>
            <td>" . htmlspecialchars($row['fecha']) . "</td>
            <td>$" . number_format($row['apertura'], 2) . "</td>
            <td>$" . number_format($row['ventas'], 2) . "</td>
            <td>$" . number_format($row['cierre'], 2) . "</td>
            <td style='color:" . ($row['diferencia'] < 0 ? "#c0392b" : "#27ae60") . ";'>" . number_format($row['diferencia'], 2) . "</td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='5'>No hay cierres registrados.</td></tr>";
      }
      $conn->close();
      ?>
    </tbody>
  </table>
</div>
      </section>

      <section id="cierre" class="contenido oculto">
        <h2>Cierre de Caja</h2>
        <p>Realiza el resumen diario y conciliación de caja.</p>
      </section>
    </main>
  </div>

  <script>
    function mostrarSeccion(id) {
      const secciones = document.querySelectorAll('.contenido');
      secciones.forEach(sec => sec.classList.add('oculto'));
      document.getElementById(id).classList.remove('oculto');
    }
    function irInventario() {
  window.location.href = 'inventario.html';
}
  </script>
</body>
</html>
