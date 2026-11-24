
<?php
require_once "../config/Conexion.php";

class Articulo
{
    public function __construct()
    {
    }

    public function insertar($idcategoria, $codigo, $nombre, $descripcion, $stock, $precio_compra, $precio_venta)
    {
        $sql = "INSERT INTO articulo
                (idcategoria, codigo, nombre, descripcion, stock, precio_compra, precio_venta, condicion)
                VALUES
                ('$idcategoria', '$codigo', '$nombre', '$descripcion', '$stock', '$precio_compra', '$precio_venta', 1)";
        return ejecutarConsulta($sql);
    }

    public function editar($idarticulo, $idcategoria, $codigo, $nombre, $descripcion, $stock, $precio_compra, $precio_venta)
    {
        $sql = "UPDATE articulo SET
                idcategoria='$idcategoria',
                codigo='$codigo',
                nombre='$nombre',
                descripcion='$descripcion',
                stock='$stock',
                precio_compra='$precio_compra',
                precio_venta='$precio_venta'
                WHERE idarticulo='$idarticulo'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idarticulo)
    {
        $sql = "UPDATE articulo SET condicion=0
                WHERE idarticulo='$idarticulo'";
        return ejecutarConsulta($sql);
    }

    public function activar($idarticulo)
    {
        $sql = "UPDATE articulo SET condicion=1
                WHERE idarticulo='$idarticulo'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idarticulo)
    {
        $sql = "SELECT * FROM articulo
                WHERE idarticulo='$idarticulo'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT a.idarticulo, a.idcategoria, c.nombre AS categoria,
                       a.codigo, a.nombre, a.descripcion, a.stock,
                       a.precio_compra, a.precio_venta, a.condicion
                FROM articulo a
                INNER JOIN categoria c ON a.idcategoria=c.idcategoria
                ORDER BY a.idarticulo DESC";
        return ejecutarConsulta($sql);
    }

    public function select()
    {
        $sql = "SELECT idarticulo, nombre FROM articulo
                WHERE condicion=1 ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }
}
