<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Create Users Table
$sql_users = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'voluntario') DEFAULT 'voluntario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $db->exec($sql_users);
    echo "Tabla usuarios verificada.<br>";
} catch(PDOException $e) {
    echo "Error creando tabla usuarios: " . $e->getMessage() . "<br>";
}

// Create Projects Table
$sql_projects = "CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    pedania_id VARCHAR(50) NOT NULL,
    pedania_nombre VARCHAR(100) NOT NULL,
    actividad VARCHAR(50) NOT NULL,
    duracion VARCHAR(100),
    frecuencia VARCHAR(100),
    material_voluntario TEXT,
    material_organizacion TEXT,
    punto_encuentro VARCHAR(255),
    transporte VARCHAR(255),
    notas_importantes TEXT,
    imagen_url VARCHAR(255) DEFAULT 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&q=80&w=1000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $db->exec($sql_projects);
    echo "Tabla proyectos verificada.<br>";
} catch(PDOException $e) {
    echo "Error creando tabla proyectos: " . $e->getMessage() . "<br>";
}

// Ensure Admin User Exists with CORRECT HASH
$email = "admin@mazarron.es";
$password = "admin";
$password_hash = password_hash($password, PASSWORD_BCRYPT);

$query = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Update existing admin
    $query_update = "UPDATE usuarios SET password_hash = :password_hash, role = 'admin' WHERE email = :email";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(":password_hash", $password_hash);
    $stmt_update->bindParam(":email", $email);
    if($stmt_update->execute()) {
        echo "Usuario Admin actualizado correctamente (Password: admin).<br>";
    } else {
        echo "Error actualizando Admin.<br>";
    }
} else {
    // Insert new admin
    $query_insert = "INSERT INTO usuarios (email, password_hash, role) VALUES (:email, :password_hash, 'admin')";
    $stmt_insert = $db->prepare($query_insert);
    $stmt_insert->bindParam(":email", $email);
    $stmt_insert->bindParam(":password_hash", $password_hash);
    if($stmt_insert->execute()) {
        echo "Usuario Admin creado correctamente (Password: admin).<br>";
    } else {
        echo "Error creando Admin.<br>";
    }
}

echo "Setup completado.";
?>
