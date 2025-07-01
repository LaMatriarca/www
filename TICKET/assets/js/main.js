/* Mostrar y ocultar menú desplegable */
const navMenu = document.getElementById('nav-menu');
const navToggle = document.getElementById('nav-toggle');
const navClose = document.getElementById('nav-close');

navToggle?.addEventListener('click', () => navMenu.classList.add('show-menu'));
navClose?.addEventListener('click', () => navMenu.classList.remove('show-menu'));

document.querySelectorAll('.nav__link').forEach(link =>
  link.addEventListener('click', () => navMenu.classList.remove('show-menu'))
);


/* Efecto de desenfoque en el header al hacer scroll */
window.addEventListener('scroll', () => {
  const header = document.getElementById('header');
  header.classList.toggle('blur-header', window.scrollY >= 50);
});

/* Mostrar botón Scroll Up */
window.addEventListener('scroll', () => {
  const scrollUp = document.getElementById('scroll-up');
  scrollUp.classList.toggle('show-scroll', window.scrollY >= 350);
});

/* Activar enlace según sección visible */
const sections = document.querySelectorAll('section[id]');
window.addEventListener('scroll', () => {
  const scrollY = window.scrollY;

  sections.forEach(current => {
    const sectionTop = current.offsetTop - 58;
    const sectionHeight = current.offsetHeight;
    const sectionId = current.getAttribute('id');
    const link = document.querySelector(`.nav__menu a[href*='${sectionId}']`);

    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
      link?.classList.add('active-link');
    } else {
      link?.classList.remove('active-link');
    }
  });
});

/* Scroll Reveal animations */
const sr = ScrollReveal({
  origin: 'top',
  distance: '40px',
  opacity: 1,
  scale: 1.1,
  duration: 2500,
  delay: 300,
});

sr.reveal(`.home__data, .about__img, .about__data, .visit__data`);
sr.reveal(`.home__image, .footer__img-1, .footer__img-2`, { rotate: { z: -15 } });
sr.reveal(`.home__bread, .about__bread`, { rotate: { z: 15 } });
sr.reveal(`.home__footer`, { scale: 1, origin: 'bottom' });
sr.reveal(`.new__card:nth-child(1) img`, { rotate: { z: -30 }, distance: 0 });
sr.reveal(`.new__card:nth-child(2) img`, { rotate: { z: 15 }, distance: 0, delay: 600 });
sr.reveal(`.new__card:nth-child(3) img`, { rotate: { z: -30 }, distance: 0, delay: 900 });
sr.reveal(`.footer__container`, { scale: 1 });
sr.reveal(`.favorite__card img`, { interval: 100, rotate: { z: 15 }, distance: 0 });

/* Carrito */
const cart = {};
const cartCount = document.getElementById("cart-count");

document.querySelectorAll(".favorite__button").forEach(button => {
  button.addEventListener("click", function () {
    const card = this.closest(".favorite__card");
    const name = card.querySelector(".favorite__title").textContent;

    cart[name] = cart[name] || { quantity: 0 };
    cart[name].quantity += 1;

    updateCartUI();
  });

    const btnVaciar = document.getElementById("empty-cart");
  if (btnVaciar) {
    btnVaciar.addEventListener("click", vaciarCarrito);
  }

});

function updateCartUI() {
  const totalItems = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
  cartCount.textContent = totalItems;
}

/* Mostrar/Ocultar carrito lateral */
const cartButton = document.getElementById('cart-button');
const closeCartButton = document.getElementById('close-cart');
const cartSidebar = document.getElementById('cart-sidebar');
const overlay = document.getElementById('overlay');
const continueShoppingButton = document.getElementById('continue-shopping');
const checkoutButton = document.getElementById('checkout');

const closeCart = () => {
  cartSidebar.style.right = '-400px';
  overlay.style.display = 'none';
};

cartButton?.addEventListener('click', () => {
  cartSidebar.style.right = '0';
  overlay.style.display = 'block';
});

closeCartButton?.addEventListener('click', closeCart);
continueShoppingButton?.addEventListener('click', closeCart);
overlay?.addEventListener('click', closeCart);

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeCart();
});




                                              // PROCESO PARA PAGAR EL PEDIDO

checkoutButton?.addEventListener('click', () => {
  alert('Procediendo al pago...');
  
});



// Funcion para agregar productos al carritop
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

document.addEventListener('DOMContentLoaded', () => {
  actualizarCarritoUI();

  // Asociar evento a todos los botones del MENU (+)
  document.querySelectorAll(".favorite__button").forEach(button => {
    button.addEventListener("click", function () {
      const card = this.closest(".favorite__card");
      const nombre = card.querySelector(".favorite__title").textContent;
      const precio = parseFloat(card.querySelector(".favorite__price").textContent.replace("$", ""));
      const imagen = card.querySelector(".favorite__img")?.getAttribute("src") || "";

      const existente = carrito.find(p => p.nombre === nombre);
      if (existente) {
        existente.cantidad += 1;
      } else {
        carrito.push({ nombre, precio, imagen, cantidad: 1 });
      }

      localStorage.setItem('carrito', JSON.stringify(carrito));
      actualizarCarritoUI();
    });
  });

   const btnVaciar = document.getElementById("empty-cart");
  if (btnVaciar) {
    btnVaciar.addEventListener("click", vaciarCarrito);
  }

});
document.getElementById("checkout").addEventListener("click", () => {
  if (carrito.length === 0) {
    alert("El carrito está vacío.");
    return;
  }

  const resumenDiv = document.getElementById("resumen-carrito");
  resumenDiv.innerHTML = "";
  let total = 0;

  carrito.forEach(p => {
    total += p.precio * p.cantidad;
    resumenDiv.innerHTML += `<p>${p.nombre} x${p.cantidad} - $${(p.precio * p.cantidad).toFixed(2)}</p>`;
  });

  document.getElementById("resumen-total").textContent = total.toFixed(2);
  document.getElementById("modal-resumen").classList.add("show");

  // Mostrar instrucciones de pago por defecto
  actualizarInstruccionesPago("efectivo");
});

// Cerrar el modal
document.querySelector(".cerrar-modal").addEventListener("click", () => {
 document.getElementById("modal-resumen").classList.remove("show");

});

// Cambio en el select de forma de pago
document.getElementById("tipo-pago").addEventListener("change", e => {
  actualizarInstruccionesPago(e.target.value);
});

function actualizarInstruccionesPago(tipo) {
  const instrucciones = document.getElementById("instrucciones-pago");
  if (tipo === "transferencia") {
    instrucciones.innerHTML = `
      <p>1. Realiza una transferencia a la cuenta:</p>
      <p><strong>BBVA 0123 4567 8901 2345</strong></p>
      <p>2. Envíanos el comprobante al WhatsApp: <strong>+52 123 456 7890</strong></p>
    `;
  } else {
    instrucciones.innerHTML = `
    <p>1. Envía tu dirección exacta al WhatsApp: <strong>+52 123 456 7890</strong></p>
      <p>1. Espera al repartidor en la hora seleccionada.</p>
      <p>2. Realiza el pago directamente en efectivo.</p>
    `;
  }
}

// Confirmar compra
document.getElementById('confirmar-compra').addEventListener('click', () => {
  if (carrito.length === 0) {
    alert('El carrito está vacío.');
    return;
  }

  const formaPago = document.getElementById('tipo-pago').value;
  const horaEntrega = document.getElementById('hora-entrega').value;

  // Preparar el carrito para enviar
  const carritoEnviar = carrito.map(item => ({
    nombre: item.nombre,
    cantidad: item.cantidad,
    subtotal: item.precio * item.cantidad
  }));

  fetch('guardar_pedido.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      carrito: carritoEnviar,
      forma_pago: formaPago,
      hora_entrega: horaEntrega
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Pedido guardado con éxito.');
      // Abrir ticket en nueva pestaña
      window.open('imprimir_delivery.php?id=' + data.id_pedido, '_blank');
      // Vaciar carrito y cerrar modal si quieres
      carrito = [];
      localStorage.setItem('carrito', JSON.stringify(carrito));
      actualizarCarritoUI();
      document.getElementById('modal-resumen').classList.remove('show');
    } else {
      alert('Error: ' + data.error);
    }
  })
  .catch(err => alert('Error al conectar con el servidor.'));
});

function vaciarCarrito() {
  document.getElementById('cart-count').textContent = '0';
  carrito = [];
  localStorage.removeItem('carrito');
  actualizarCarritoUI();
}

function actualizarCarritoUI() {
  const contenedor = document.getElementById("cart-items");
  const totalSpan = document.getElementById("cart-total");
  const subtotalSpan = document.getElementById("cart-subtotal");
  const descuentoSpan = document.getElementById("cart-discount");
  const cartCount = document.getElementById("cart-count"); 
  
  contenedor.innerHTML = "";

  let total = 0;
  let cantidadTotal = 0; 

  carrito.forEach(item => {
    const itemHTML = `
      <div class="item-carrito" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <img src="${item.imagen}" width="40" height="40" />
        <div style="flex-grow: 1;">
          <strong>${item.nombre}</strong><br>
          <span>Cantidad: ${item.cantidad}</span>
        </div>
        <span>$${(item.precio * item.cantidad).toFixed(2)}</span>
      </div>
    `;
    total += item.precio * item.cantidad;
    cantidadTotal += item.cantidad; 
    contenedor.innerHTML += itemHTML;
  });

  subtotalSpan.textContent = `$${total.toFixed(2)}`;
  descuentoSpan.textContent = `$0.00`;
  totalSpan.textContent = `$${total.toFixed(2)}`;
  cartCount.textContent = cantidadTotal; 
}


                                                  // ANIMACIONES DE LOS BOTONES DEL CARRITO

    document.querySelectorAll('#continue-shopping, #checkout, #empty-cart').forEach(button => {
        button.addEventListener('mouseenter', function() {
            if(this.id === 'continue-shopping') {
                this.querySelector('span:last-child').style.width = '100%';
            } 
            else if(this.id === 'checkout') {
                const shine = this.querySelector('span:last-child');
                shine.style.left = '70%';
                shine.style.top = '30%';
                shine.style.opacity = '1';
                shine.style.transform = 'scale(10)';
                this.style.boxShadow = '0 0 15px rgba(215, 168, 110, 0.7)';
            }
            else if(this.id === 'empty-cart') {
                this.querySelector('span:last-child').style.left = '100%';
                this.style.color = '#3E2723';
            }
        });

        button.addEventListener('mouseleave', function() {
            if(this.id === 'continue-shopping') {
                this.querySelector('span:last-child').style.width = '0';
            } 
            else if(this.id === 'checkout') {
                const shine = this.querySelector('span:last-child');
                shine.style.opacity = '0';
                shine.style.transform = 'scale(0)';
                this.style.boxShadow = '0 0 0 rgba(215, 168, 110, 0.7)';
            }
            else if(this.id === 'empty-cart') {
                this.querySelector('span:last-child').style.left = '-100%';
                this.style.color = '#E8D5B5';
            }
        });
    });


                                                  // FUNCIONES PARA BOTON DE LOG