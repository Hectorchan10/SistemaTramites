<?php

$host = "localhost";
$username = "root";          // Usuario por defecto en XAMPP
$password = "";              // Contraseña vacía en XAMPP
$dbname = "tramites";        // ← ¡Usa "tramites", no "344desarrollo"!

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

function limpiar($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
