
<?php
require_once "../config/Conexion.php";

class Compra
{
    public function proveedores()
    {
        global $conexion;
        return $conexion->query(
            "SELECT idproveedor, razon_social, documento
             FROM proveedor
             WHERE condicion = 1
             ORDER BY razon_social"
        );
    }

    public function articulos()
    {
        global $conexion;
        return $conexion->query(
            "SELECT idarticulo, codigo, nombre, stock, precio_compra
             FROM articulo
             WHERE condicion = 1
             ORDER BY nombre"
        );
    }

    public function listar()
    {
        global $conexion;
        return $conexion->query(
            "SELECT c.idcompra, c.fecha_hora, p.razon_social,
                    c.tipo_comprobante, c.serie, c.correlativo,
                    c.impuesto, c.total, c.estado, c.observacion
             FROM compra c
             INNER JOIN proveedor p ON c.idproveedor = p.idproveedor
             ORDER BY c.idcompra DESC"
        );
    }

    public function obtener($idcompra)
    {
        global $conexion;

        $stmt = $conexion->prepare(
            "SELECT idcompra, idproveedor, tipo_comprobante,
                    serie, correlativo, observacion, impuesto, total, estado
             FROM compra WHERE idcompra = ?"
        );
        $stmt->bind_param("i", $idcompra);
        $stmt->execute();
        $compra = $stmt->get_result()->fetch_assoc();

        if (!$compra) {
            return null;
        }

        $stmt = $conexion->prepare(
            "SELECT d.idarticulo, a.nombre, a.codigo,
                    d.cantidad, d.precio_compra, d.subtotal
             FROM detalle_compra d
             INNER JOIN articulo a ON a.idarticulo = d.idarticulo
             WHERE d.idcompra = ?"
        );
        $stmt->bind_param("i", $idcompra);
        $stmt->execute();

        $detalles = [];
        $resultado = $stmt->get_result();

        while ($fila = $resultado->fetch_assoc()) {
            $detalles[] = $fila;
        }

        return [
            "compra" => $compra,
            "detalles" => $detalles
        ];
    }

    private function validarDetalles($detalles)
    {
        if (!is_array($detalles) || count($detalles) === 0) {
            throw new Exception("Agrega al menos un artículo.");
        }

        $lineas = [];

        foreach ($detalles as $detalle) {
            $id = (int)($detalle["idarticulo"] ?? 0);
            $cantidad = (int)($detalle["cantidad"] ?? 0);
            $precio = (float)($detalle["precio_compra"] ?? 0);

            if ($id <= 0 || $cantidad <= 0 || $precio <= 0) {
                throw new Exception("Verifica los artículos, cantidades y precios.");
            }

            $lineas[] = [
                "idarticulo" => $id,
                "cantidad" => $cantidad,
                "precio" => $precio,
                "subtotal" => round($cantidad * $precio, 2)
            ];
        }

        return $lineas;
    }

    private function insertarDetalles($idcompra, $lineas)
    {
        global $conexion;

        foreach ($lineas as $linea) {
            $id = $linea["idarticulo"];
            $cantidad = $linea["cantidad"];
            $precio = $linea["precio"];
            $subtotal = $linea["subtotal"];

            $consulta = $conexion->prepare(
                "SELECT idarticulo FROM articulo
                 WHERE idarticulo = ? AND condicion = 1 FOR UPDATE"
            );
            $consulta->bind_param("i", $id);
            $consulta->execute();

            if ($consulta->get_result()->num_rows === 0) {
                throw new Exception("Uno de los artículos no existe o está inactivo.");
            }

            $insertar = $conexion->prepare(
                "INSERT INTO detalle_compra
                 (idcompra, idarticulo, cantidad, precio_compra, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $insertar->bind_param(
                "iiidd",
                $idcompra,
                $id,
                $cantidad,
                $precio,
                $subtotal
            );

            if (!$insertar->execute()) {
                throw new Exception("No se pudo guardar el detalle de compra.");
            }

            $actualizar = $conexion->prepare(
                "UPDATE articulo
                 SET stock = stock + ?, precio_compra = ?
                 WHERE idarticulo = ?"
            );
            $actualizar->bind_param("idi", $cantidad, $precio, $id);

            if (!$actualizar->execute()) {
                throw new Exception("No se pudo actualizar el inventario.");
            }
        }
    }

    private function devolverStockAnterior($idcompra)
    {
        global $conexion;

        $stmt = $conexion->prepare(
            "SELECT idarticulo, cantidad
             FROM detalle_compra
             WHERE idcompra = ?"
        );
        $stmt->bind_param("i", $idcompra);
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($detalle = $resultado->fetch_assoc()) {
            $id = (int)$detalle["idarticulo"];
            $cantidad = (int)$detalle["cantidad"];

            $consulta = $conexion->prepare(
                "SELECT stock FROM articulo
                 WHERE idarticulo = ? FOR UPDATE"
            );
            $consulta->bind_param("i", $id);
            $consulta->execute();
            $articulo = $consulta->get_result()->fetch_assoc();

            if (!$articulo || (int)$articulo["stock"] < $cantidad) {
                throw new Exception(
                    "No se puede anular o editar: parte del artículo ya fue utilizada en otras operaciones."
                );
            }

            $actualizar = $conexion->prepare(
                "UPDATE articulo SET stock = stock - ?
                 WHERE idarticulo = ?"
            );
            $actualizar->bind_param("ii", $cantidad, $id);

            if (!$actualizar->execute()) {
                throw new Exception("No se pudo ajustar el inventario.");
            }
        }
    }

    public function registrar(
        $idproveedor,
        $tipo,
        $serie,
        $correlativo,
        $observacion,
        $detalles
    ) {
        global $conexion;

        if ($idproveedor <= 0) {
            return "Selecciona un proveedor válido.";
        }

        try {
            $lineas = $this->validarDetalles($detalles);
            $conexion->begin_transaction();

            $subtotal = 0;
            foreach ($lineas as $linea) {
                $subtotal += $linea["subtotal"];
            }

            $impuesto = round($subtotal * 0.18, 2);
            $total = round($subtotal + $impuesto, 2);
            $estado = "Registrada";

            $stmt = $conexion->prepare(
                "INSERT INTO compra
                 (idproveedor, tipo_comprobante, serie, correlativo,
                  impuesto, total, estado, observacion)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isssddss",
                $idproveedor,
                $tipo,
                $serie,
                $correlativo,
                $impuesto,
                $total,
                $estado,
                $observacion
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo registrar la compra.");
            }

            $idcompra = $conexion->insert_id;
            $this->insertarDetalles($idcompra, $lineas);

            $conexion->commit();
            return "Compra registrada correctamente. Código: " . $idcompra;

        } catch (Throwable $e) {
            $conexion->rollback();
            return "Error al registrar la compra: " . $e->getMessage();
        }
    }

    public function editar(
        $idcompra,
        $idproveedor,
        $tipo,
        $serie,
        $correlativo,
        $observacion,
        $detalles
    ) {
        global $conexion;

        if ($idcompra <= 0 || $idproveedor <= 0) {
            return "Datos de compra no válidos.";
        }

        try {
            $lineas = $this->validarDetalles($detalles);
            $conexion->begin_transaction();

            $bloqueo = $conexion->prepare(
                "SELECT estado FROM compra WHERE idcompra = ? FOR UPDATE"
            );
            $bloqueo->bind_param("i", $idcompra);
            $bloqueo->execute();
            $actual = $bloqueo->get_result()->fetch_assoc();

            if (!$actual || $actual["estado"] !== "Registrada") {
                throw new Exception("La compra no existe o ya está anulada.");
            }

            $this->devolverStockAnterior($idcompra);

            $borrar = $conexion->prepare(
                "DELETE FROM detalle_compra WHERE idcompra = ?"
            );
            $borrar->bind_param("i", $idcompra);

            if (!$borrar->execute()) {
                throw new Exception("No se pudieron actualizar los detalles.");
            }

            $subtotal = 0;
            foreach ($lineas as $linea) {
                $subtotal += $linea["subtotal"];
            }

            $impuesto = round($subtotal * 0.18, 2);
            $total = round($subtotal + $impuesto, 2);

            $actualizar = $conexion->prepare(
                "UPDATE compra SET idproveedor = ?, tipo_comprobante = ?,
                 serie = ?, correlativo = ?, observacion = ?,
                 impuesto = ?, total = ? WHERE idcompra = ?"
            );
            $actualizar->bind_param(
                "issssddi",
                $idproveedor,
                $tipo,
                $serie,
                $correlativo,
                $observacion,
                $impuesto,
                $total,
                $idcompra
            );

            if (!$actualizar->execute()) {
                throw new Exception("No se pudo actualizar la compra.");
            }

            $this->insertarDetalles($idcompra, $lineas);

            $conexion->commit();
            return "Compra actualizada correctamente.";

        } catch (Throwable $e) {
            $conexion->rollback();
            return "Error al editar la compra: " . $e->getMessage();
        }
    }

    public function anular($idcompra)
    {
        global $conexion;

        if ($idcompra <= 0) {
            return "Código de compra no válido.";
        }

        try {
            $conexion->begin_transaction();

            $stmt = $conexion->prepare(
                "SELECT estado FROM compra WHERE idcompra = ? FOR UPDATE"
            );
            $stmt->bind_param("i", $idcompra);
            $stmt->execute();
            $compra = $stmt->get_result()->fetch_assoc();

            if (!$compra || $compra["estado"] !== "Registrada") {
                throw new Exception("La compra no existe o ya está anulada.");
            }

            $this->devolverStockAnterior($idcompra);

            $anular = $conexion->prepare(
                "UPDATE compra SET estado = 'Anulada' WHERE idcompra = ?"
            );
            $anular->bind_param("i", $idcompra);

            if (!$anular->execute()) {
                throw new Exception("No se pudo anular la compra.");
            }

            $conexion->commit();
            return "Compra anulada correctamente. El inventario fue actualizado.";

        } catch (Throwable $e) {
            $conexion->rollback();
            return "Error al anular la compra: " . $e->getMessage();
        }
    }
}
?>
