<?php

$mysqli = new mysqli("localhost", "tramites_DHC", "DHC", "tramites_DHC");
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}
// else{
//     die("conexión realizada");
// }

$mysqli->set_charset("utf8mb4");

function limpiar($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
// ¿Qué hace falta o se podría mejorar?
// Variables de conexión centralizadas (en lugar de hardcodear valores):
// $host = "localhost";---------------------------------------------------------
// $user = "root";
// $pass = "";
// $db   = "venta_pc";
// $mysqli = new mysqli($host, $user, $pass, $db);--------------------------------------------------------
