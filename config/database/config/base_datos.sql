CREATE DATABASE IF NOT EXISTS sistema_tramites
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sistema_tramites;


CREATE TABLE tbl_estado_tramite (
    id_estado_tramite INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE tbl_tipo_tramite (
    id_tipo_tramite INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
);


CREATE TABLE tbl_area (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    correo VARCHAR(50),
    descripcion VARCHAR (200),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE tbl_rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);
CREATE TABLE tbl_usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    correo VARCHAR(100) UNIQUE,
    dpi VARCHAR(15) UNIQUE,
    img varchar (100),
    activo BOOLEAN DEFAULT TRUE,
    id_area INT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_area) REFERENCES tbl_area(id_area) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES tbl_rol(id_rol) ON DELETE RESTRICT ON UPDATE CASCADE
);


-- ========================================
--  Tablas de trámites y remitentes
CREATE TABLE tbl_remitente (
    id_remitente INT AUTO_INCREMENT PRIMARY KEY,
    tipo_persona ENUM('Natural', 'Jurídica') NOT NULL,
    telefono VARCHAR(20),
    dpi VARCHAR(13) UNIQUE,
    correo VARCHAR(100),
    razon_social VARCHAR(200)
);

CREATE TABLE tbl_tramite_remitente (
    id_tramite_remitente INT AUTO_INCREMENT PRIMARY KEY,
    asunto VARCHAR(100) NOT NULL UNIQUE,
    tipo_tramite VARCHAR (100) NOT NULL,
    documentos VARCHAR (100) NOT NULL,
    mensaje VARCHAR (100) NOT NULL
);

-- ========================================

CREATE TABLE tbl_tramite (
    id_tramite INT AUTO_INCREMENT PRIMARY KEY,
    asunto VARCHAR(255) NOT NULL,
    mensaje TEXT,
    base_legal TEXT,
    tags VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_caducidad DATE,
    id_remitente INT NOT NULL,
    id_estado_tramite INT NOT NULL,
    id_tipo_tramite INT NOT NULL,
    id_usuario_creador INT,
    FOREIGN KEY (id_remitente) REFERENCES tbl_remitente(id_remitente) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_estado_tramite) REFERENCES tbl_estado_tramite(id_estado_tramite) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_tipo_tramite) REFERENCES tbl_tipo_tramite(id_tipo_tramite) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_usuario_creador) REFERENCES tbl_usuario(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE tbl_seguimiento (
    id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_tramite INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    detalle TEXT NOT NULL,
    documento_adjunto VARCHAR(255),
    id_usuario_seguimiento INT,
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_usuario_seguimiento) REFERENCES tbl_usuario(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE tbl_derivacion (
    id_derivacion INT AUTO_INCREMENT PRIMARY KEY,
    id_tramite INT NOT NULL,
    id_usuario_envia INT NOT NULL,
    id_usuario_recibe INT NOT NULL,
    fecha_derivacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    nota TEXT,
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_usuario_envia) REFERENCES tbl_usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_usuario_recibe) REFERENCES tbl_usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tbl_tramite_area (
    id_tramite INT NOT NULL,
    id_area INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_tramite, id_area),
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_area) REFERENCES tbl_area(id_area) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tbl_tramite_usuario_asignado (
    id_tramite INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_tramite, id_usuario),
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES tbl_usuario(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ========================================
--  Datos iniciales
-- ========================================

INSERT INTO tbl_rol (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema y gestión completa de usuarios y trámites'),
('Usuario', 'Acceso básico para visualizar o dar seguimiento a trámites propios');

INSERT INTO tbl_estado_tramite (nombre, descripcion) VALUES
('Pendiente', 'El trámite ha sido ingresado y está pendiente de revisión.'),
('En Proceso', 'El trámite está siendo analizado o revisado.'),
('Finalizado', 'El trámite ha sido resuelto exitosamente.'),
('Rechazado', 'El trámite fue rechazado o no cumple los requisitos.');

INSERT INTO tbl_tipo_tramite (nombre, descripcion) VALUES
('Solicitud General', 'Trámite genérico para peticiones o solicitudes.'),
('Queja', 'Trámite de denuncia o reclamo.'),
('Permiso', 'Solicitud de autorización formal.'),
('Revisión', 'Trámite para revisión de documentos o procesos.');

INSERT INTO tbl_area (nombre, correo, descripcion) VALUES
('Secretaría','departamentosecretaria@gmail.com','Departamento encargado de registrar todos los archivos'),
('Jurídico','departamentojuridico@gmail.com','Departamento encargado de todas las cuestiones juridicas'),
('Finanzas','departamentofinanzas@gmail.com','Departamento encargado de las finanzas'),
('Dirección General','departamentogeneral@gmail.com','Departamento encargado de toda la direccion general');
