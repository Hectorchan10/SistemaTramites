CREATE DATABASE sistema_tramites
	character set utf8mb4
    collate utf8mb4_unicode_ci;

USE sistema_tramites;

CREATE TABLE tbl_estado_tramite (
    id_estado_tramite INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    PRIMARY KEY (id_estado_tramite)
);

CREATE TABLE tbl_tipo_tramite (
    id_tipo_tramite INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (id_tipo_tramite)
);

CREATE TABLE tbl_area (
    id_area INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (id_area)
);

CREATE TABLE tbl_usuario (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    correo VARCHAR(100) UNIQUE,
    rol VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    dpi VARCHAR(15) UNIQUE,
    id_area INT,
    PRIMARY KEY (id_usuario),
    FOREIGN KEY (id_area) REFERENCES tbl_area(id_area)
);

CREATE TABLE tbl_remitente (
    id_remitente INT NOT NULL AUTO_INCREMENT,
    tipo_persona VARCHAR(50) NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    direccion VARCHAR(255),
    nit VARCHAR(20) UNIQUE,
    correo VARCHAR(100),
    telefono VARCHAR(20),
    razon_social VARCHAR(200),
    PRIMARY KEY (id_remitente)
);

CREATE TABLE tbl_tramite (
    id_tramite INT NOT NULL AUTO_INCREMENT,
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
    
    PRIMARY KEY (id_tramite),
    FOREIGN KEY (id_remitente) REFERENCES tbl_remitente(id_remitente),
    FOREIGN KEY (id_estado_tramite) REFERENCES tbl_estado_tramite(id_estado_tramite),
    FOREIGN KEY (id_tipo_tramite) REFERENCES tbl_tipo_tramite(id_tipo_tramite),
    FOREIGN KEY (id_usuario_creador) REFERENCES tbl_usuario(id_usuario)
);


CREATE TABLE tbl_seguimiento (
    id_seguimiento INT NOT NULL AUTO_INCREMENT,
    id_tramite INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    detalle TEXT NOT NULL,
    documento_adjunto VARCHAR(255),
    id_usuario_seguimiento INT,
    PRIMARY KEY (id_seguimiento),
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite),
    FOREIGN KEY (id_usuario_seguimiento) REFERENCES tbl_usuario(id_usuario)
);

CREATE TABLE tbl_derivacion (
    id_nota INT NOT NULL AUTO_INCREMENT,
    id_tramite INT NOT NULL
    id_usuario_envia INT NOT NULL, 
    id_usuario_recibe INT NOT NULL,
    fecha_derivacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    nota TEXT,
    PRIMARY KEY (id_nota),
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite),
    FOREIGN KEY (id_usuario_envia) REFERENCES tbl_usuario(id_usuario),
    FOREIGN KEY (id_usuario_recibe) REFERENCES tbl_usuario(id_usuario)
);

CREATE TABLE tbl_tramite_area (
    id_tramite INT NOT NULL,
    id_area INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_tramite, id_area), 
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite),
    FOREIGN KEY (id_area) REFERENCES tbl_area(id_area)
);

CREATE TABLE tbl_tramite_usuario_asignado (
    id_tramite INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_tramite, id_usuario), 
    FOREIGN KEY (id_tramite) REFERENCES tbl_tramite(id_tramite),
    FOREIGN KEY (id_usuario) REFERENCES tbl_usuario(id_usuario)
);