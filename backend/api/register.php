<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../controllers/AuthController.php';

$auth = new AuthController();
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($data['email']) && isset($data['password'])) {
        $auth->register($data);
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    }
} else {
    http_response_code(405);
}
?>
