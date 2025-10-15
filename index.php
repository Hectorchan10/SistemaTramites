<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Trámites - Guatemala DHS</title>
    <link rel="stylesheet" href="/style/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>

  <body>
    <header>
      <!-- el navbar  -->
      <div class="topbar" role="navigation" aria-label="Barra de navegación principal">
        <div class="logo">
          <a href="/index.php">
            <h1>Tramites</h1>
          </a>
        </div>
        <button class="hamburger" aria-label="Menú">☰</button>
        <nav>
          <ul>
            <li><a href="/index.php">Inicio</a></li>
            <li><a href="#contacto">Contacto</a></li>
            <li><a href="/views/login.php">Login</a></li>
          </ul>
        </nav>
      </div>
      <!-- el navbar  -->

      <!-- el baner -->
      <div class="hero-banner">
        <div class="hero-content">
          <h1>Trámites</h1>
          <p>Facilitando tus gestiones de manera rápida y eficiente</p>
          <a href="#tramites" class="hero-btn">Consultar</a>
          <a href="nuevotramite.html" class="hero-btn">Nuevo Trámite</a>
        </div>
      </div>
      <!-- el baner -->
    </header>

    <!-- contenido  -->
    <main>
      <section class="solicitud-documentos" id="contacto">
        <div class="left-section">
          <span class="label">SOLICITUD DE DOCUMENTOS</span>
          <h2 class="title">Solicita tus documentos en cualquier momento</h2>
          <p class="description">
            Gracias por elegir nuestro servicio. Ofrecemos trámites de documentos rápidos y
            sencillos en línea, totalmente gratis. ¡Comparte nuestro sitio web con tus amigos!
          </p>
        </div>
        <div class="form-section">
          <form>
            <div class="form-group">
              <label for="fullName">Nombre completo</label>
              <input
                type="text"
                class="input-field"
                placeholder="Nombre completo"
                id="fullName"
                required
                autocomplete="name"
              />
            </div>
            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input
                type="email"
                class="input-field"
                placeholder="Correo electrónico"
                id="email"
                required
                autocomplete="email"
              />
            </div>
            <div class="form-group">
              <label for="documents">Documentos que solicitas</label>
              <textarea
                class="input-field"
                placeholder="Documentos que solicitas"
                id="documents"
                required
                rows="4"
              ></textarea>
            </div>
            <button type="submit" class="submit-btn">Enviar solicitud</button>
          </form>
        </div>
      </section>
    </main>
    <!-- contenido  -->

    <!--el footer -->
    <footer>
      <div class="footer-content">
        <!-- Sobre Contramites -->
        <div class="footer-section">
          <h3>DHC</h3>
          <p>Facilitando tus trámites de forma rápida y segura.</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>

        <!-- Enlaces Rápidos -->
        <div class="footer-section">
          <h3>Enlaces Rápidos</h3>
          <ul>
            <li><a href="/index.html">Inicio</a></li>
            <li><a href="#Tramites">Tramites</a></li>
            <li><a href="#contacto">Contacto</a></li>
            <li><a href="/views/login.html">Login</a></li>
          </ul>
        </div>

        <!-- Contacto -->
        <div class="footer-section">
          <h3>Contacto</h3>
          <ul class="footer-contact">
            <li><i class="fas fa-envelope"></i> DHC@gmail.com</li>
            <li><i class="fas fa-phone"></i> +502 4647 7826</li>
            <li><i class="fas fa-map-marker-alt"></i> Quezsaltenengo de Guatemala, GT</li>
          </ul>
        </div>

        <!-- Horarios -->
        <div class="footer-section">
          <h3>Horario de Atención</h3>
          <ul class="footer-hours">
            <li>Lunes - Viernes: 8:00 AM - 5:00 PM</li>
            <li>Sábado: 9:00 AM - 1:00 PM</li>
            <li>Domingo: Cerrado</li>
          </ul>
        </div>
      </div>

      <!-- Copyright -->
      <div class="footer-copyright">
        <p>&copy; 2025 DHC Todos los derechos reservados.</p>
      </div>
    </footer>
    <!--el footer -->
  </body>
</html>
