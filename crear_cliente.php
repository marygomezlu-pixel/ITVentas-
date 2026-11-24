<?php
require 'config/Conexion.php';

$sql = "CREATE TABLE IF NOT EXISTS cliente (
    idcliente INT NOT NULL AUTO_INCREMENT,
    tipo_documento VARCHAR(20) DEFAULT 'DNI',
    documento VARCHAR(20) DEFAULT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    direccion VARCHAR(180) DEFAULT NULL,
    condicion TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idcliente),
    UNIQUE KEY documento (documento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (!$conexion->query($sql)) {
    die("ERROR CLIENTE: " . $conexion->error);
}

$resultado = $conexion->query("SHOW COLUMNS FROM venta LIKE 'idcliente'");

if ($resultado->num_rows === 0) {
    $sql = "ALTER TABLE venta ADD COLUMN idcliente INT NULL AFTER idventa, ADD INDEX idx_venta_cliente (idcliente)";
    if (!$conexion->query($sql)) {
        die("ERROR VENTA: " . $conexion->error);
    }
}

$resultado = $conexion->query("
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'venta'
    AND COLUMN_NAME = 'idcliente'
    AND REFERENCED_TABLE_NAME = 'cliente'
");

if ($resultado->num_rows === 0) {
    if (!$conexion->query("ALTER TABLE venta ADD CONSTRAINT fk_venta_cliente FOREIGN KEY (idcliente) REFERENCES cliente(idcliente)")) {
        die("ERROR RELACION: " . $conexion->error);
    }
}

echo "OK: tabla cliente creada y relacionada con venta.";
