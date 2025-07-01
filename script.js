console.log("¡script.js cargado!");
// Inicializar eventos
document.addEventListener("DOMContentLoaded", () => {
     
  const btnGenerar = document.getElementById("generar-ticket");

  if (btnGenerar) {
    console.log("✅ Botón Generar encontrado");

    btnGenerar.addEventListener("click", () => {
      console.log("🟢 Clic en Generar Ticket");
      if (carrito.length === 0) {
        alert("El carrito está vacío");
        return;
      }
      mostrarTicket();
    });
  } else {
    console.error("❌ No se encontró el botón #generar-ticket");
  }
  const btnVaciar = document.getElementById("vaciar-carrito");
    if (btnVaciar) {
    btnVaciar.addEventListener("click", () => {
      if (carrito.length === 0) {
        alert("El carrito ya está vacío.");
        return;
      }
      if (confirm("¿Estás seguro que deseas vaciar el carrito?")) {
        carrito = [];
        actualizarCarrito();
      }
    });
  }  else {
    console.error("❌ No se encontró el botón #generar-ticket");
  }

  const btnSalir = document.getElementById("btn-salir");

  if (btnSalir) {
    btnSalir.addEventListener("click", () => {
      const confirmacion = confirm("¿Deseas salir?\nLa caja se mantendrá abierta.");

      if (confirmacion) {
        // Redireccionar o cerrar sesión
        window.location.href = "login.php"; // o el archivo a donde quieras redirigir
      }
    });
  } else {
    console.error("❌ No se encontró el botón #btn-salir");
  }

  

  
  document.getElementById("imprimir-ticket").addEventListener("click", imprimirTicket);
  document.getElementById("calcular-cambio").addEventListener("click", calcularCambio);
  document.querySelector(".close").addEventListener("click", cerrarModal);

  // Filtro por categorías
  const botones = document.querySelectorAll(".btn-categoria");
  const productos = document.querySelectorAll(".producto");

  botones.forEach(boton => {
    boton.addEventListener("click", () => {
      const categoria = boton.dataset.categoria;

      botones.forEach(b => b.classList.remove("active"));
      boton.classList.add("active");

      productos.forEach(p => {
        p.style.display =
          categoria === "todos" || p.dataset.categoria === categoria
            ? "block"
            : "none";
            
      });
    });
  });
});

  
let carrito = [];

// Agregar al carrito
function agregarAlCarrito(id, nombre, precio) {
   const productoExistente = carrito.find(item => item.id === id);

  if (productoExistente) {
    productoExistente.cantidad += 1;
  } else {
    carrito.push({ id, nombre, precio, cantidad: 1 });
  }

  actualizarCarrito();
}

// Actualizar carrito en pantalla
function actualizarCarrito() {
  const contenedor = document.getElementById("items-carrito");
  const total = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);

  contenedor.innerHTML = carrito.map((item, index) => `
    <div class="item-carrito">
      <span>${item.nombre}</span>
      <span>$${item.precio.toFixed(2)}</span>
      <div>
      <button class="btn-cantidad" onclick="disminuirCantidad(${index})">-</button>
      <span>${item.cantidad}</span>
      <button class="btn-cantidad" onclick="aumentarCantidad(${index})">+</button>
      <button class="btn-eliminar" onclick="eliminarDelCarrito(${index})">🗑️</button>
    </div>
  </div>
    </div>
  `).join("");

  document.getElementById("total").textContent = `$${total.toFixed(2)}`;
}
function aumentarCantidad(index) {
  carrito[index].cantidad += 1;
  actualizarCarrito();
}

function disminuirCantidad(index) {
  if (carrito[index].cantidad > 1) {
    carrito[index].cantidad -= 1;
  } else {
    eliminarDelCarrito(index);
  }
  actualizarCarrito();
}

function eliminarDelCarrito(index) {
  carrito.splice(index, 1);
  actualizarCarrito();
}
// Mostrar modal de ticket
function mostrarTicket() {
  const modal = document.getElementById("ticket-modal");
  const detalle = document.getElementById("ticket-detalle");
  const total = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);

  detalle.innerHTML = carrito.map(item => `
    <div>
      <span>${item.nombre}</span>
      <span>$${item.precio.toFixed(2)}</span>
    </div>
  `).join("");

  document.getElementById("ticket-total").textContent = `$${total.toFixed(2)}`;
  modal.style.display = "block";
}

// Calcular cambio
function calcularCambio() {
  const total = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);
  const pago = parseFloat(document.getElementById("pago-recibido").value);

  if (isNaN(pago) || pago < total) {
    alert("El pago debe ser igual o mayor al total");
    return;
  }

  const cambio = pago - total;
  document.getElementById("ticket-cambio").textContent = `$${cambio.toFixed(2)}`;
  document.getElementById("monto-recibido").textContent = pago.toFixed(2);
}

// Imprimir ticket (guardar venta y abrir PDF)
function imprimirTicket() {
  const total = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);
  const pago = parseFloat(document.getElementById("pago-recibido").value);
  const cambio = pago - total;

  if (isNaN(pago)) {
    alert("Por favor ingresa un monto numérico");
    return;
  }
  if (pago < total) {
    alert(`El pago ($${pago.toFixed(2)}) no cubre el total ($${total.toFixed(2)})`);
    return;
  }

  fetch("guardar_venta.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      total: total,
      productos: carrito.map(p => ({
        id: p.id,
        nombre: p.nombre,
        precio: p.precio,
        cantidad: p.cantidad || 1
      }))
    })
  })
  .then(res => res.json())
  .then(data => {
    console.log("Respuesta del servidor:", data);
    if (data.status === "ok") {
      window.open("imprimir_ticket.php?id=" + data.venta_id, "_blank");
      alert("Venta registrada correctamente");
      carrito = [];
      actualizarCarrito();
      cerrarModal();
      limpiarTicket();
    } else {
      alert("Error al guardar la venta");
    }
  });
}

// Cerrar modal
function cerrarModal() {
  document.getElementById("ticket-modal").style.display = "none";
}

// Limpiar ticket
function limpiarTicket() {
  document.getElementById("pago-recibido").value = "";
  document.getElementById("monto-recibido").textContent = "0.00";
  document.getElementById("ticket-cambio").textContent = "$0.00";
}

