<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../controllers/ProjectController.php';

// Auth Check could be done here or in Controller. Doing here for simplicity of wrapper.
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$controller = new ProjectController();
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->create($data);
} else {
    http_response_code(405);
}
?>
