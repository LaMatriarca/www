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
                <div id="login" class="nav__login">
                    <button id="login-button" class="nav__login-button">
                        <i class="ri-login-box-fill"></i>
                    </button>
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
                           Aqui va su descripcion
                        </p>
                     </div>
   
                     <img src="assets/img/chocolatin.png" alt="image" class="new__img">
                  </article>
   
                  <article class="new__card">
                     <div class="new__data">
                        <h2 class="new__title">Baguette</h2>
                        <p class="new__description">
                           Aqui va su descripcion.
                        </p>
                     </div>
   
                     <img src="assets/img/Baguette.png" alt="image" class="new__img">
                  </article>
   
                  <article class="new__card">
                     <div class="new__data">
                        <h2 class="new__title">Cubilete</h2>
                        <p class="new__description">
                           Aqui va su descripcion.
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

               <img src="assets/img/donas3x2.jpg" alt="image" class="about__img">
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
            </div>
            <div class="help-button">
               <br><br><br><br><br>
  <button onclick="document.getElementById('ayuda-descargas').classList.toggle('visible')">
    Ayuda
  </button>
</div>

<!-- Apartado de descargas -->
<div id="ayuda-descargas" class="help-panel">
  <h3>Descargas de ayuda</h3>
  <ul>
    <li><a href="assets/guiarapida.pdf" download>Descargar Guía Rápida</a></li>
    <li><a href="assets/ManualDeUsuarioMigasoft.pdf" download>Descargar Manual de Usuario</a></li>
  </ul>
  <button onclick="document.getElementById('ayuda-descargas').classList.remove('visible')">Cerrar</button>
</div>

         </div>
         

         <span class="footer__copy">
            &#169; Todos los derechos reservados por Migasoft
         </span>
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
<script src="assets/js/scrollreveal.min.js"></script>

      <!-- MAIN JS-->
      <script src="assets/js/main.js"></script>
   </body>
</html>