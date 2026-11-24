CREATE TABLE IF NOT EXISTS proveedor (
    idproveedor INT NOT NULL AUTO_INCREMENT,
    tipo_documento VARCHAR(20) NOT NULL DEFAULT 'RUC',
    documento VARCHAR(20) NOT NULL,
    razon_social VARCHAR(150) NOT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    contacto VARCHAR(100) DEFAULT NULL,
    condicion TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (idproveedor),
    UNIQUE KEY uk_proveedor_documento (documento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS compra (
    idcompra INT NOT NULL AUTO_INCREMENT,
    idproveedor INT NOT NULL,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo_comprobante VARCHAR(30) DEFAULT NULL,
    serie VARCHAR(20) DEFAULT NULL,
    correlativo VARCHAR(30) DEFAULT NULL,
    impuesto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado VARCHAR(20) NOT NULL DEFAULT 'Registrada',
    observacion TEXT DEFAULT NULL,
    PRIMARY KEY (idcompra),
    KEY idx_compra_proveedor (idproveedor),
    CONSTRAINT fk_compra_proveedor
        FOREIGN KEY (idproveedor) REFERENCES proveedor(idproveedor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS detalle_compra (
    iddetalle_compra INT NOT NULL AUTO_INCREMENT,
    idcompra INT NOT NULL,
    idarticulo INT NOT NULL,
    cantidad INT NOT NULL,
    precio_compra DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (iddetalle_compra),
    KEY idx_detalle_compra (idcompra),
    KEY idx_detalle_articulo (idarticulo),
    CONSTRAINT fk_detalle_compra
        FOREIGN KEY (idcompra) REFERENCES compra(idcompra),
    CONSTRAINT fk_detalle_articulo
        FOREIGN KEY (idarticulo) REFERENCES articulo(idarticulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
