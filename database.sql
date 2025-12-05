CREATE DATABASE IF NOT EXISTS voluntariado_db;
USE voluntariado_db;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'voluntario') DEFAULT 'voluntario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Proyectos/Actividades
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    pedania_id VARCHAR(50) NOT NULL,
    pedania_nombre VARCHAR(100) NOT NULL,
    actividad VARCHAR(50) NOT NULL,    -- Tipo de actividad (filtro)
    duracion VARCHAR(100),
    frecuencia VARCHAR(100),
    material_voluntario TEXT,
    material_organizacion TEXT,
    punto_encuentro VARCHAR(255),
    transporte VARCHAR(255),
    notas_importantes TEXT,
    imagen_url VARCHAR(255) DEFAULT 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&q=80&w=1000', -- Imagen por defecto
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar Usuario Admin por defecto (Password: admin)
INSERT INTO usuarios (email, password_hash, role) VALUES 
('admin@mazarron.es', '$2y$10$YourHashedPasswordHere', 'admin') 
ON DUPLICATE KEY UPDATE email=email; 
-- Nota: Para 'admin', el hash de 'admin' es $2y$10$8.1p... (Se debe generar con password_hash en PHP)
-- Usaremos un placeholder o lo generaremos en PHP para insertar si no existe.

-- Datos de prueba para Proyectos
INSERT INTO proyectos (titulo, descripcion, pedania_id, pedania_nombre, actividad, imagen_url) VALUES 
('Limpieza de Playa del Puerto', 'Únete a nosotros para limpiar la costa y proteger la vida marina.', 'puerto', 'Puerto de Mazarrón', 'medio_ambiente', 'https://images.unsplash.com/photo-1618477461853-5f8dd1209c47?auto=format&fit=crop&q=80&w=1000'),
('Acompañamiento a Mayores', 'Visita y compañía a personas mayores en la residencia de Mazarrón.', 'mazarron', 'Mazarrón (Casco Urbano)', 'mayores', 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&q=80&w=1000'),
('Taller de Lectura Infantil', 'Ayuda a niños a mejorar su lectura en la biblioteca municipal.', 'mazarron', 'Mazarrón (Casco Urbano)', 'educacion', 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&q=80&w=1000');
