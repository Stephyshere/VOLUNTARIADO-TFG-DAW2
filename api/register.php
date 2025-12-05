<?php
require_once 'db_connect.php';

// Asegurarse de que el método es POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit();
}

// Recibir y limpiar datos JSON
$data = json_decode(file_get_contents("php://input"), true);

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

// 1. Validar si el usuario ya existe
$stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Este correo ya está registrado."]);
    $stmt_check->close();
    $conn->close();
    exit();
}
$stmt_check->close();

// 2. Hashear la contraseña por seguridad
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// 3. Insertar el nuevo usuario
$stmt_insert = $conn->prepare("INSERT INTO usuarios (email, password_hash, role) VALUES (?, ?, 'voluntario')");
$stmt_insert->bind_param("ss", $email, $password_hash);

if ($stmt_insert->execute()) {
    echo json_encode(["success" => true, "message" => "Registro exitoso. Ahora puedes iniciar sesión."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar el usuario."]);
}

$stmt_insert->close();
$conn->close();
?>