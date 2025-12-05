<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../controllers/ProjectController.php';

session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$controller = new ProjectController();
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->delete($data['id']);
} else {
    http_response_code(405);
}
?>
