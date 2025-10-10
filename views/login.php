<?php
session_start();

// Si ya está logueado, redirigir según su rol
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin') {
        header('Location: /views/admin/dashboard.php');
    } else {
        header('Location: /views/empleado/dashboard.php');
    }
    exit;
}

// Mensaje de error (si existe)
$error = '';
$success = '';

// Procesar el formulario solo si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos';
    } else {
        // Conexión a la base de datos
        $host = 'localhost';
        $dbname = 'tramites';
        $db_user = 'desarrollo 344'; // Cambia si es necesario
        $db_pass = '344desarrollo';     // Cambia si es necesario

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Buscar usuario por email
            $stmt = $pdo->prepare("SELECT id, email, password, rol FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar contraseña (texto plano, como en tu ejemplo)
            if ($usuario && $password === $usuario['password']) {
                // Iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['rol'] = $usuario['rol'];

                // Redirigir según rol
                if ($usuario['rol'] === 'admin') {
                    header('Location: /views/admin/dashboard.php');
                } else {
                    header('Location: /views/empleado/dashboard.php');
                }
                exit;
            } else {
                $error = 'Credenciales incorrectas';
            }
        } catch (PDOException $e) {
            $error = 'Error en la conexión a la base de datos';
            // En producción, no muestres $e->getMessage() por seguridad
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="/style/style.css" />
</head>

<body>
  <header>
    <div class="topbar" role="navigation" aria-label="Barra de navegación principal">
      <div class="logo">
        <a href="/index.html">
          <h1>Tramites</h1>
        </a>
      </div>
      <nav>
        <ul>
          <li><a href="/index.html">Inicio</a></li>
          <li><a href="#contacto">Contacto</a></li>
          <li><a href="/views/login.php">Login</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <center>
      <div class="login-container">
        <div class="login-header">
          <h1>Bienvenido</h1>
          <p>Inicia sesión en tu cuenta</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="message error" style="display:block; margin-bottom:15px;">
          <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="/views/login.php">
          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com"
              value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
              required />
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required />
          </div>
          <a href="/views/admin/dashboard.php" style="color: red;">administrador</a>
          <br>
          <a href="/views/empleado/dashboard.php" style="color: red;">empleado</a>
          <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="forgot-password">
          <a href="/views/recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>
      </div>
    </center>
  </main>
</body>

</html>