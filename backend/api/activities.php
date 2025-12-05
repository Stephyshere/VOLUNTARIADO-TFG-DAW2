<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../controllers/ProjectController.php';

$controller = new ProjectController();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id'])) {
        $controller->readOne($_GET['id']);
    } else {
        $pedania = isset($_GET['pedania']) ? $_GET['pedania'] : 'all';
        $actividad = isset($_GET['actividad']) ? $_GET['actividad'] : 'all';
        $controller->list($pedania, $actividad);
    }
} else {
    http_response_code(405);
}
?>
