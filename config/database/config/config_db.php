<?php

$host = "localhost";
$username = "admin_tramites";          // Usuario 
$password = "DHS_tr@mites2025";              // Contraseña 
$dbname = "sistema_tramites";        // ← Nombre de BDD

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

function limpiar($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
