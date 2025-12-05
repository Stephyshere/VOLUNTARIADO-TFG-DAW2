<?php
// Configuración de la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '1234');
define('DB_NAME', 'voluntariado_db');

// Crear la conexión
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    // Es mejor no mostrar el error de base de datos directamente al usuario en producción
    die(json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]));
}

// Configurar juego de caracteres a UTF8
$conn->set_charset("utf8mb4");

// Opcional: Configurar CORS si el frontend y el backend están en dominios diferentes
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
?>