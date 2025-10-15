CREATE DATABASE sistema_tramites
	character set utf8mb4
    collate utf8mb4_unicode_ci;

USE sistema_tramites;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario varchar (50),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro datetime default current_timestamp, 
    rol ENUM('admin', 'empleado') NOT NULL default 'empleado',
    foto VARCHAR (100),
    DPI VARCHAR(13) NOT NULL UNIQUE
);

INSERT INTO usuarios (nombre_usuario, email, password, rol, foto, DPI)
VALUES 
    ('Carlos Admin', 'carlos.admin@example.com','admin123', 'admin', 'carlos.jpg', 1234567890123),
    ('Ana Empleado', 'ana.empleado@example.com', 'empleado123', 'empleado', 'ana.jpg', 9876543210987);