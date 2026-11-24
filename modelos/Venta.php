<?php
require_once "../config/Conexion.php";

class Venta
{
    public function articulos()
    {
        global $conexion;
        return $conexion->query(
            "SELECT idarticulo, codigo, nombre, stock, precio_venta
             FROM articulo
             WHERE condicion = 1
             ORDER BY nombre"
        );
    }

    public function listar()
    {
        global $conexion;
        return $conexion->query(
            "SELECT idventa, fecha_hora, tipo_comprobante,
                    serie, correlativo, impuesto, total, estado
             FROM venta
             ORDER BY idventa DESC"
        );
    }

    public function registrar($tipo, $serie, $correlativo, $observacion, $detalles)
    {
        global $conexion;

        if (!is_array($detalles) || count($detalles) === 0) {
            return "Agrega al menos un producto a la venta";
        }

        $conexion->begin_transaction();

        try {
            $subtotalGeneral = 0;
            $lineas = [];

            foreach ($detalles as $detalle) {
                $id = (int)($detalle["idarticulo"] ?? 0);
                $cantidad = (int)($detalle["cantidad"] ?? 0);

                if ($id <= 0 || $cantidad <= 0) {
                    throw new Exception("Verifica los productos y cantidades");
                }

                $consulta = $conexion->prepare(
                    "SELECT stock, precio_venta
                     FROM articulo
                     WHERE idarticulo = ? AND condicion = 1
                     FOR UPDATE"
                );
                $consulta->bind_param("i", $id);
                $consulta->execute();
                $articulo = $consulta->get_result()->fetch_assoc();

                if (!$articulo) {
                    throw new Exception("Un producto no existe o está inactivo");
                }

                if ((int)$articulo["stock"] < $cantidad) {
                    throw new Exception(
                        "Stock insuficiente para " . $id .
                        ". Disponible: " . $articulo["stock"]
                    );
                }

                $precio = (float)$articulo["precio_venta"];
                $subtotal = $cantidad * $precio;
                $subtotalGeneral += $subtotal;

                $lineas[] = [
                    "id" => $id,
                    "cantidad" => $cantidad,
                    "precio" => $precio,
                    "subtotal" => $subtotal
                ];
            }

            $impuesto = round($subtotalGeneral * 0.18, 2);
            $total = round($subtotalGeneral + $impuesto, 2);
            $estado = "Registrada";

            $insertar = $conexion->prepare(
                "INSERT INTO venta
                (tipo_comprobante, serie, correlativo, impuesto, total, estado, observacion)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $insertar->bind_param(
                "sssddss",
                $tipo, $serie, $correlativo,
                $impuesto, $total, $estado, $observacion
            );

            if (!$insertar->execute()) {
                throw new Exception("No se pudo registrar la venta");
            }

            $idventa = $conexion->insert_id;

            foreach ($lineas as $linea) {
                $id = $linea["id"];
                $cantidad = $linea["cantidad"];
                $precio = $linea["precio"];
                $subtotal = $linea["subtotal"];

                $detalle = $conexion->prepare(
                    "INSERT INTO detalle_venta
                    (idventa, idarticulo, cantidad, precio_venta, subtotal)
                    VALUES (?, ?, ?, ?, ?)"
                );
                $detalle->bind_param(
                    "iiidd", $idventa, $id, $cantidad, $precio, $subtotal
                );

                if (!$detalle->execute()) {
                    throw new Exception("No se pudo guardar el detalle");
                }

                $actualizar = $conexion->prepare(
                    "UPDATE articulo SET stock = stock - ?
                     WHERE idarticulo = ? AND stock >= ?"
                );
                $actualizar->bind_param("iii", $cantidad, $id, $cantidad);

                if (!$actualizar->execute() || $actualizar->affected_rows !== 1) {
                    throw new Exception("No se pudo descontar el inventario");
                }
            }

            $conexion->commit();
            return "Venta registrada correctamente. Código: " . $idventa;

        } catch (Throwable $e) {
            $conexion->rollback();
            return "Error al registrar la venta: " . $e->getMessage();
        }
    }
}
?>
