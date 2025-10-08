CREATE DATABASE tramites;

USE tramites;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') NOT NULL
);

ALTER TABLE usuarios 
ADD COLUMN reset_token VARCHAR(255) NULL,
ADD COLUMN reset_expires DATETIME NULL;

INSERT INTO usuarios (email, password, rol) VALUES
('admin@dejemplo.com', 'contraseña123', 'admin'),
('empleado@dejemplo.com', 'contraseña123', 'empleado');
