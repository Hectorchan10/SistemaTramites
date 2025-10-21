<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero</title>
    <link rel="stylesheet" href="/style/tableroadmin.css">
</head>
<body>
    <?php include '../sidebaradministrador.php'; ?>
    <h1>Administrador</h1>
    <nav style="margin-bottom: 20px;">
    <a href="/logout.php" class="btn-logout">Cerrar sesión</a>
    </nav>
 
    <div class="tarjeta-mensaje">
        <h1>¡Bienvenido!</h1>
        <p>Has iniciado sesión correctamente</p>
    </div>

    <div class="tablero" id="tablero">
        <div id="total-usuarios" class="tarjeta-usuarios">

        </div>

        <div id="total-tramites" class="tarjeta-tramites">
    
        </div>

        <div id="total-areas" class="tarjeta-areas">

        </div>
    </div>

    


</body>
</html>