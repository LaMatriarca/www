<?php 
session_start();

$conn = new mysqli("localhost", "root", "", "artepan");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta productos
$result = $conn->query("SELECT id, nombre, precio, imagen FROM productos WHERE stock > 0");


?> 
<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">


   
      




<link rel="stylesheet" href="style.css" />

      <!--FAVICON-->
      <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">


   <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">


 
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.7.0/remixicon.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const usuarioLogueado = <?php echo isset($_SESSION['cliente_nombre']) ? 'true' : 'false'; ?>;
  const clienteNombre = <?php echo isset($_SESSION['cliente_nombre']) ? json_encode($_SESSION['cliente_nombre']) : '""'; ?>;
</script>
      <!--CSS -->
      <link rel="stylesheet" href="assets/css/styles.css">

      <title>Panaderia Artepan</title>
   </head>
   <body>
      
<?php if (isset($_SESSION['cliente_nombre'])): ?>
  <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?>!</p>
  
<?php else: ?>
  <p>Bienvenido a ARTEPAN, visitante.</p>
<?php endif; ?>
      <!--HEADER -->
      <header class="header" id="header">
    <nav class="nav container">
        <a href="#" class="nav__logo">ARTEPAN</a>
        

        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="#home" class="nav__link active-link">Inicio</a>
                </li>

                <li class="nav__item">
                    <a href="#new" class="nav__link">Especialidades</a>
                </li>

                <li class="nav__item">
                    <a href="#about" class="nav__link">Nosotros</a>
                </li>

                <li class="nav__item">
                    <a href="#favorite" class="nav__link">Menu</a>
                </li>
                <li class="nav__item">
                    <a href="#promo" class="nav__link">Promos</a>
                </li>

                <li class="nav__item">
                    <a href="#visit" class="nav__link">Ubicación</a>
                </li>
                
            </ul>

            <!-- Aquí los botones ya bien ubicados (afuera del ul) -->
            <div class="nav__buttons">
                <!-- Botón Carrito -->
                <div id="cart" class="nav__cart">
                    <button id="cart-button" class="nav__cart-button">
                        <i class="ri-shopping-cart-fill"></i>
                    </button>
                    <span id="cart-count">0</span>
                </div>

                <!-- Botón Login -->
              <!-- Botón Login -->
                <!-- Botón Login / Usuario -->
<div id="login" class="nav__login">
    <?php if (isset($_SESSION['cliente_nombre'])): ?>
        <div class="nav__user-menu">
            <button class="nav__login-button">
                <i class="ri-user-3-fill"></i> <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?>
            </button>
            <div class="nav__submenu">
                <p>Hola, <?php echo htmlspecialchars($_SESSION['cliente_nombre']); ?></p>
                 <button id="ver-pedidos-btn" class="ver-pedidos-link">Ver mis pedidos</button>
                 <br><br>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
            </div>
        </div>
    <?php else: ?>
        <button id="login-button" class="nav__login-button">
            <i class="ri-login-box-fill"></i>
        </button>
    <?php endif; ?>
</div>
            </div>
            

            <!-- Botón cerrar -->
            <div class="nav__close" id="nav-close">
                <i class="ri-close-line"></i>
            </div>

            <img src="assets/img/bread-4.png" alt="image" class="nav__img-1">
            <img src="assets/img/bread-1.png" alt="image" class="nav__img-2">
        </div>

        <!-- Botón de menú hamburguesa -->
        <div class="nav__toggle" id="nav-toggle">
            <i class="ri-menu-line"></i>
        </div>
    
            

              
 
      <script>
    // Agrega este script para manejar el clic del botón
    document.getElementById('login-button').addEventListener('click', function() {
        window.location.href = 'login.php'; // Asegúrate de que el nombre del archivo coincida
    });
</script>


                                                                  <!-- VENTANA DEL CARRITO  -->                                                             

<div id="cart-sidebar" style="
    position: fixed;
    top: 0;
    right: -400px;
    width: 400px;
    height: 100%;
    background-color: hsla(28, 27%, 26%, 0.301);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: -5px 0 15px rgba(139, 90, 43, 0.2);
    transition: right 0.3s ease;
    z-index: 1000;
    padding: 20px;
    overflow-y: auto;
    font-family: 'Roboto', sans-serif;
">
    <!-- Botón de cerrar -->
    <button id="close-cart" style="
        position: absolute;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #E8D5B5;
    ">×</button>
    
    <!-- Título -->
    <h2 style="
        color: #FFF3E0;
        font-weight: 700;
        margin-top: 40px;
        border-bottom: 1px solid #D7A86E;
        padding-bottom: 10px;
    ">Tu Carrito</h2>

    <!-- Lista de productos -->
    <div id="cart-items" style="
        margin: 20px 0;
        color: #E8D5B5;
    "></div>

    <!-- Resumen del carrito -->
    <div id="cart-summary" style="
        border-top: 1px solid #D7A86E;
        padding-top: 20px;
        color: #E8D5B5;
    ">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <span>Subtotal:</span>
            <span id="cart-subtotal" style="color: #D7A86E; font-weight: 600; font-size: 18px;">$0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <span>Descuento:</span>
            <span id="cart-discount" style="color: #B8A38D;">$0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: bold; margin: 25px 0;">
            <span style="color: #D7A86E; font-size: 22px;">Total:</span>
            <span id="cart-total" style="color: #FFF3E0; font-weight: 700; font-size: 22px;">$0.00</span>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 30px;">
        <!-- 1. Botón "Seguir Comprando" -->
        <button id="continue-shopping" style="
            padding: 14px;
            background: transparent;
            border: 2px solid #D7A86E;
            color: #E8D5B5;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.65, 0, 0.35, 1);
            position: relative;
            overflow: hidden;
            z-index: 1;
        ">
            <span style="position: relative; z-index: 2;">Seguir Comprando</span>
            <span style="
                position: absolute;
                top: 0;
                left: 0;
                width: 0;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(215, 168, 110, 0.2));
                transition: width 0.4s cubic-bezier(0.65, 0, 0.35, 1);
                z-index: 1;
            "></span>
        </button>

        <!-- 2. Botón "Comprar Ahora" -->
        <button id="checkout" style="
            padding: 14px;
            background: #D7A86E;
            color: #3E2723;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 0 rgba(215, 168, 110, 0.7);
        ">
            <span style="position: relative; z-index: 2;">Comprar Ahora</span>
            <span style="
                position:absolute;
                width: 70px;
                height: 15px;
                background: rgba(255, 255, 255, 0.7);
                border-radius: 50%;
                transform: scale(0);
                opacity: 0;
                transition: transform 0.9s, opacity 0.3s;
            "></span>
        </button>


        
        <!-- 3. Botón "Vaciar Carrito" -->
        <button id="empty-cart" style="
            padding: 14px;
            background: transparent;
            border: 2px solid #B8A38D;
            color: #E8D5B5;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        ">
            <span style="position: relative; z-index: 2;">Vaciar Carrito</span>
            <span style="
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(184, 163, 141, 0.3));
                transition: left 0.5s ease;
                z-index: 1;
            "></span>
        </button>
    </div>
</div>

      <!-- Fondo oscuro -->
         <div id="overlay" style="position: fixed; top: 0; left: 0; width: 79%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; display: none;"></div>

      </nav>
</header>



      
      <!-- MAIN =-->
      <main class="main">
         
         <!--INICIO-->
         <section class="home section" id="home">
            <img src="assets/img/fondo.jpg" alt="image" class="home__bg">
            <div class="home__shadow"></div>

            <div class="home__container container grid">
               <div class="home__data">
                  <h1 class="home__title">
                     <br> Panaderia Artesanal
                  </h1>
                  

                  <img src="assets/img/bread-1.png" alt="image" class="home__bread">
               </div>

               <div class="home__image">
                  <img src="assets/img/logo.png" alt="image" class="home__img">
               </div>

               <footer class="home__footer">
                  <div class="home__location">
                     <i class="ri-map-pin-line"></i>
                     <span>Valle de las flores<br>Edo.Mex</span>
                  </div>

                  <div class="home__social">
                     <a href="https://www.facebook.com/" target="_blank">
                        <i class="ri-facebook-circle-line"></i>
                     </a>

                     <a href="https://www.instagram.com/" target="_blank">
                        <i class="ri-instagram-line"></i>
                     </a>

                     <a href="https://www.youtube.com/" target="_blank">
                        <i class="ri-youtube-line"></i>
                     </a>
                  </div>
               </footer>
            </div>
         </section>

         <!--ESPECIALIDADES-->
         <section class="new section" id="new">
            <h2 class="section__title">ESPECIALIDADES</h2>

            <div class="new__container container grid">
               <div class="new__content grid">
                  <article class="new__card">
                     <div class="new__data">
                        <h2 class="new__title">Chocolatin</h2>
                        <p class="new__description">
                           Delicioso pan suave y esponjoso, relleno de una generosa barra de chocolate que se derrite en cada bocado. Ideal para acompañar un café o disfrutar como un antojo dulce.
                        </p>
                     </div>
   
                     <img src="assets/img/chocolatin.png" alt="image" class="new__img">
                  </article>
   
                  <article class="new__card">
                     <div class="new__data">
                        <h2 class="new__title">Baguette</h2>
                        <p class="new__description">
                           Clásico pan francés de corteza dorada y crujiente, con un interior suave y aireado. Perfecto para preparar sándwiches, acompañar platillos o disfrutar con mantequilla.

                        </p>
                     </div>
   
                     <img src="assets/img/Baguette.png" alt="image" class="new__img">
                  </article>
   
                  <article class="new__card">
                     <div class="new__data">
                        <h2 class="new__title">Cubilete</h2>
                        <p class="new__description">
                          Panecillo dulce con una textura suave y esponjosa, coronado con un toque de azúcar. Su sabor tradicional lo convierte en un favorito de todas las edades.


                        </p>
                     </div>
   
                     <img src="assets/img/cubilete.png" alt="image" class="new__img">
                  </article>
               </div>
    
            </div>
         </section>

         <!--NOSOTROS-->
         <section class="about section" id="about">
            <div class="about__container container grid">
               <div class="about__data">
                  <h2 class="section__title">NOSOTROS</h2>

                  <p class="about__description" style="font-size: 20px;text-align: justify; font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif" >
                    En ARTEPAN, cada pan y cada bocado cuentan una historia de tradición, pasión y calidad. Somos una panadería artesanal dedicada a crear productos únicos, elaborados a mano con ingredientes naturales, sin conservadores y con procesos que respetan el tiempo y el sabor auténtico de la panadería tradicional.

                  </p>

                  <p class="about__description" style="font-size: 20px;text-align: justify; font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif">
                     Nuestro compromiso es ofrecerte una experiencia sensorial incomparable. Desde el aroma cálido de un pan recién horneado hasta la textura perfecta de un croissant crujiente o la suavidad de un pan de masa madre, en ARTESA combinamos técnicas ancestrales con un toque contemporáneo para llevar a tu mesa lo mejor del pan artesanal.

                  </p>
                  <img src="assets/img/bread-2.png" alt="image" class="about__bread">
               </div>

               <img src="assets/img/nosotros.png" alt="image" class="about__img">
            </div>
         </section>


         
         <!--MENU-->
         <section class="favorite section" id="favorite">
            <h2 class="section__title">MENU</h2>

            <div class="favorite__container container grid">
               
               <div class="favorite__container container grid">
  <?php while($row = $result->fetch_assoc()): ?>
    <article class="favorite__card">
      <img src="assets/img/<?php echo $row['imagen'] ?: 'default.jpg'; ?>" alt="" class="favorite__img">

      <h3 class="favorite__title"><?php echo htmlspecialchars($row['nombre']); ?></h3>
      <span class="favorite__price">$<?php echo number_format($row['precio'], 2); ?></span>

      <button class="favorite__button button">
                     <i class="ri-add-line"></i>
      </button>
      <button class="button favorite__button">
        <i class="bx bx-cart-add"></i>
      </button>
    </article>
  <?php endwhile; ?>
</div>

            </div>
         </section>


         
         <!--UBICACION-->
         <section class="visit section" id="visit">
            <div class="visit__container">
               <img src="assets/img/visitanos.jpeg" alt="image" class="visit__bg">
               <div class="visit__shadow"></div>
               
               <div class="visit__content container grid">
                  <div class="visit__data">
                     <h2 class="section__title">Visitanos</h2>
                     <p class="visit__description">
                        Endulzamos tu vida
                     </p>
   
                     <a href="https://maps.app.goo.gl/FCtNaZY8qZQ3GM4z5" class="button" target="_blank">Ubicacion</a>
                  </div>
               </div>
            </div>
         </section>

         <!--promos-->
         <section class="promo section" id="promo">
            <div class="about__container container grid">
               <div class="about__data">
                  <h2 class="section__title">Promociones</h2>

                  <p class="about__description" style="font-size: 20px; text-align: justify; font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif">
   ¡Aprovecha nuestras promociones exclusivas en tienda! Solo al visitarnos podrás disfrutar de descuentos especiales en una selección de nuestros panes artesanales. Ven y llévate 3 piezas y paga solo 2, o disfruta de nuestros combos de café con pan a precios irresistibles. Estas ofertas están disponibles por tiempo limitado y únicamente en nuestro establecimiento físico. ¡Te esperamos para que vivas la experiencia ARTEPAN!
</p>

                  <img src="assets/img/bread-2.png" alt="image" class="about__bread">
               </div>

               <img src="assets/img/anunciobaguette.gif" alt="image" class="about__img">
            </div>
         </section>
      </main>



      <!--Parte de abaja de la pagina footer-->
      <footer class="footer">
         <div class="footer__container container grid">
            <div>
               <a href="#" class="footer__logo">ARTEPAN</a>
               <p class="footer__description">
                  Ven a probar el mejor pan <br> de Mexico.
               </p>
            </div>

            <div class="footer__content grid">
               <div>
                  <h3 class="footer__title">Dirección</h3>

                  <ul class="footer__list">
                     <li>
                        <address class="footer__info">Valle de las flores<br>Edo.Mex</address>
                     </li>
                     
                     <li>
                        <address class="footer__info">8AM - 10PM</address>
                     </li>
                     <div style="position: absolute; top: 20px; right: 20px;">


                  </ul>
               </div>

               <div>
                  <h3 class="footer__title">Contactos</h3>

                  <ul class="footer__list">
                     <li>
                        <address class="footer__info">ARTEPAN@Gmail.com</address>
                     </li>

                     <li>
                        <address class="footer__info">+55 987 654 321</address>
                     </li>
                  </ul>
               </div>

               <div>
                  <h3 class="footer__title">Siguenos en <br>nuestras redes</h3>

                  <div class="footer__social">
                     <a href="https://www.facebook.com/" target="_blank">
                        <i class="ri-facebook-circle-line"></i>
                     </a>

                     <a href="https://www.instagram.com/" target="_blank">
                        <i class="ri-instagram-line"></i>
                     </a>

                     <a href="https://www.youtube.com/" target="_blank">
                        <i class="ri-youtube-line"></i>
                     </a>
                  </div>
               </div>
               <div>
  <h3 class="footer__title">Legal</h3>
  <ul class="footer__list">
    <li>
      <button onclick="document.getElementById('panel-copyright').classList.toggle('visible')" style="background: none; border: none; color: inherit; font-size: 15px; cursor: pointer; padding: 0;">
        Derechos de autor
      </button>
    </li>
  </ul>

  
</div>
<div id="panel-copyright" class="copyright-panel">
  <h3>Créditos / Derechos de autor</h3>
<p>
  Este sitio web ha sido desarrollado con fines informativos y comerciales para la panadería <strong>ARTEPAN</strong>. Algunos de los elementos visuales como íconos, tipografías y estilos fueron obtenidos de recursos libres y de uso público, respetando los términos de uso establecidos por sus respectivos autores.
</p>

<ul>
  <li>📷 Algunas fotografías, especialmente las de productos reales como panes, baguettes y especialidades, son propiedad de <strong>ARTEPAN</strong> y fueron tomadas directamente en nuestro local. Queda prohibida su reproducción sin autorización.</li>
  <li>🖼️ Otras imágenes de apoyo y ambientación fueron tomadas de bancos de imágenes libres de derechos como <a href="https://www.freepik.com/" target="_blank">Freepik</a> y <a href="https://unsplash.com/" target="_blank">Unsplash</a>, en cumplimiento con sus respectivas licencias.</li>
  <li>🎨 Los íconos utilizados en esta web provienen del proyecto <a href="https://remixicon.com/" target="_blank">Remix Icon</a>, que ofrece gráficos gratuitos para proyectos personales y comerciales.</li>
  <li>🔤 Tipografías y estilos visuales fueron adaptados desde <a href="https://fonts.google.com/" target="_blank">Google Fonts</a> y otros frameworks CSS modernos.</li>
</ul>

<p>
  Si consideras que algún contenido de este sitio infringe derechos de autor o deseas solicitar su retirada, puedes contactarnos al correo <a href="mailto:artepan@gmail.com">artepan@gmail.com</a>.
</p>

<p style="font-style: italic; font-size: 0.95em; color: #777;">
  Última actualización de esta sección: julio 2025.
</p>
  <button onclick="document.getElementById('panel-copyright').classList.remove('visible')">
    Cerrar
  </button>
</div>

            </div>
           
               <br><br><br><br><br>
  
</div>

<!-- Apartado de descargas -->


         </div>
         

         <span class="footer__copy">
            &#169; Todos los derechos reservados por Migasoft
         </span>

         <div class="copyright-button">
  
</div>

      </footer>
      

      <!--botón de volver arriba-->
      <a href="#" class="scrollup" id="scroll-up">
         <i class="ri-arrow-up-line"></i>
      </a>

      <!--SCROLLREVEAL-->
      

<!-- Modal de Resumen -->
<div id="modal-resumen" class="modal">
  <div class="modal-content">
    <span class="cerrar-modal">&times;</span>
    <h2>Resumen de tu compra</h2>
    <div id="resumen-carrito"></div>

    <p><strong>Total:</strong> $<span id="resumen-total">0.00</span></p>

    <label for="tipo-pago"><strong>Forma de pago:</strong></label>
    <select id="tipo-pago">
      <option value="efectivo">Efectivo</option>
      <option value="transferencia">Transferencia</option>
    </select>

    <div id="instrucciones-pago"></div>

    <!-- Nueva sección para hora de entrega -->
    <div class="entrega-contenedor">
  <label for="hora-entrega"><strong>Hora de entrega:</strong></label>
  <select id="hora-entrega" class="hora-select">
    <option value="">Selecciona una hora...</option>
    <option value="8:00">8:00 AM</option>
    <option value="9:00">9:00 AM</option>
    <option value="10:00">10:00 AM</option>
    <option value="11:00">11:00 AM</option>
    <option value="12:00">12:00 del mediodía</option>
    <option value="13:00">1:00 PM</option>
    <option value="14:00">2:00 PM</option>
  </select>
</div>

    <button id="confirmar-compra" class="button">Confirmar Compra</button>
  </div>
</div>
<!-- Modal de Pedidos -->
<div id="modal-pedidos" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="cerrar-modal" id="cerrar-pedidos">&times;</span>
    <h2>Mis Pedidos</h2>
    <div id="contenido-pedidos">Cargando...</div>
  </div>
</div>

 <a href="#" class="help-float" id="help-float">
  <i class="ri-question-line"></i>
</a>

<div id="ayuda-descargas" class="help-panel">
  <h3>Descargas de ayuda</h3>
  <ul>
    <li><a href="assets/img/GuiaRapidaMigasoft.pdf" download>Descargar Guía Rápida</a></li>
    <li><a href="assets/img/ManualDeUsuarioMigasoft.pdf" download>Descargar Manual de Usuario</a></li>
  </ul>
  <button onclick="document.getElementById('ayuda-descargas').classList.remove('visible')">Cerrar</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const userMenuBtn = document.querySelector('.nav__login-button');
  const userSubmenu = document.querySelector('.nav__submenu');
  const modalPedidos = document.getElementById('modal-pedidos');
  const contenidoPedidos = document.getElementById('contenido-pedidos');
  const cerrarModal = document.getElementById('cerrar-pedidos');
  const verPedidosBtn = document.getElementById('ver-pedidos-btn');

  // Mostrar y ocultar submenú
  if (userMenuBtn && userSubmenu) {
    userMenuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      userSubmenu.classList.toggle('visible');
    });

    // Cierra el submenú al hacer clic fuera
    document.addEventListener('click', function (e) {
      if (!userSubmenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
        userSubmenu.classList.remove('visible');
      }
    });
  }

  // Asignar funcionalidad botón "Ver mis pedidos"
  if (verPedidosBtn && modalPedidos && contenidoPedidos) {
    verPedidosBtn.addEventListener('click', function () {
      modalPedidos.classList.add('show');
      contenidoPedidos.innerHTML = 'Cargando...';

      fetch('ver_pedidos.php')
        .then(res => res.json()) // Cambiamos a JSON para procesar datos
        .then(data => {
          if (data.success) {
            if (data.pedidos.length === 0) {
              contenidoPedidos.innerHTML = '<p>No tienes pedidos aún.</p>';
              return;
            }

            // Construimos el HTML con pedidos y detalles
            let html = '';
            data.pedidos.forEach(pedido => {
              html += `
                <div class="pedido">
                  <h3>Pedido ${pedido.folio}</h3>
                  <p><strong>Total:</strong> $${pedido.total}</p>
                  <p><strong>Forma de pago:</strong> ${pedido.forma_pago}</p>
                  <p><strong>Hora de entrega:</strong> ${pedido.hora_entrega}</p>
                  <p><strong>Fecha:</strong> ${pedido.fecha}</p>
                  <h4>Detalles:</h4>
                  <ul>
                    ${pedido.detalles.map(det => `<li>${det.cantidad} x ${det.nombre} - $${det.subtotal}</li>`).join('')}
                  </ul>
                </div>
                <hr>
              `;
            });

            contenidoPedidos.innerHTML = html;
          } else {
            contenidoPedidos.innerHTML = `<p>Error: ${data.error}</p>`;
          }
        })
        .catch(err => {
          contenidoPedidos.innerHTML = '<p>Error al cargar pedidos.</p>';
          console.error(err);
        });
    });
  }

  // Cerrar modal al dar click en la "x"
  if (cerrarModal && modalPedidos) {
    cerrarModal.addEventListener('click', function () {
      modalPedidos.classList.remove('show');
    });
  }

  // Botón login (por si no está logueado)
  const loginBtn = document.getElementById('login-button');
  if (loginBtn) {
    loginBtn.addEventListener('click', function () {
      window.location.href = 'login.php';
    });
  }

   const helpBtn = document.getElementById('help-float');
  const ayudaPanel = document.getElementById('ayuda-descargas');

  if (helpBtn && ayudaPanel) {
    helpBtn.addEventListener('click', function (e) {
      e.preventDefault();
      ayudaPanel.classList.toggle('visible');
    });
  }
});
</script>
<script>
  const usuarioLogueado = <?php echo isset($_SESSION['cliente_nombre']) ? 'true' : 'false'; ?>;
</script>



<script src="assets/js/scrollreveal.min.js"></script>

      <!-- MAIN JS-->
      <script src="assets/js/main.js"></script>
     
   </body>
   
</html>