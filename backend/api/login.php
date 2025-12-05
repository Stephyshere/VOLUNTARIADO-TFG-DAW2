<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../controllers/AuthController.php';

$auth = new AuthController();
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($data['email']) && isset($data['password'])) {
        $auth->login($data);
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    }
} else {
    http_response_code(405);
}
?>
