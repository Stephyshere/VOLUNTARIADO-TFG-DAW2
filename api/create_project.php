<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Asumimos que $data contiene todos los campos del formulario de crear-actividad.html

$titulo = $conn->real_escape_string($data['title']);
$descripcion = $conn->real_escape_string($data['description']);
$pedania_id = $conn->real_escape_string($data['pedaniaId']);
$pedania_nombre = $conn->real_escape_string($data['pedania']);
$actividad = $conn->real_escape_string($data['activity']);
$duracion = $conn->real_escape_string($data['duration']);
$frecuencia = $conn->real_escape_string($data['frequency']);

// Convertir los arrays de materiales a texto con saltos de línea para MySQL
$material_vol = $conn->real_escape_string(implode("\n", $data['vol_material']));
$material_org = $conn->real_escape_string(implode("\n", $data['org_material']));

$encuentro = $conn->real_escape_string($data['meeting']);
$transporte = $conn->real_escape_string($data['transport']);
$notas = $conn->real_escape_string($data['notes']);


$sql = "INSERT INTO proyectos (titulo, descripcion, pedania_id, pedania_nombre, actividad, duracion, frecuencia, material_voluntario, material_organizacion, punto_encuentro, transporte, notas_importantes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssss", $titulo, $descripcion, $pedania_id, $pedania_nombre, $actividad, $duracion, $frecuencia, $material_vol, $material_org, $encuentro, $transporte, $notas);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Proyecto creado con éxito.", "id" => $conn->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Error al crear el proyecto: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>