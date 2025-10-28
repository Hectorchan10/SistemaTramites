<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero</title>
    <link rel="stylesheet" href="/style/tableroadmin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../sidebaradministrador.php'; ?>

    <div class="header-dashboard">
        <h1>Tramite virtual</h1>
        <a href="/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>

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

    <div class="grafica-section">
        <h2>Trámites por Mes</h2>
        <canvas id="tramitesChart" width="400" height="200"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('tramitesChart').getContext('2d');
        const tramitesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                datasets: [{
                    label: 'Trámites',
                    data: [12, 19, 3, 5, 2, 3, 10, 15, 8, 12, 6, 9], // Datos de ejemplo, reemplazar con datos reales
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
