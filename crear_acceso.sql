CREATE TABLE rol (
    idrol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150),
    condicion TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE usuario (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    idrol INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(20),
    documento VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    imagen VARCHAR(255),
    condicion TINYINT(1) DEFAULT 1,
    FOREIGN KEY (idrol) REFERENCES rol(idrol)
) ENGINE=InnoDB;

CREATE TABLE permiso (
    idpermiso INT AUTO_INCREMENT PRIMARY KEY,
    idrol INT NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    acceso TINYINT(1) DEFAULT 0,
    UNIQUE KEY permiso_rol_modulo (idrol, modulo),
    FOREIGN KEY (idrol) REFERENCES rol(idrol)
) ENGINE=InnoDB;

INSERT INTO rol (nombre, descripcion) VALUES
('Administrador', 'Acceso general al sistema'),
('Vendedor', 'Acceso a operaciones de ventas');

