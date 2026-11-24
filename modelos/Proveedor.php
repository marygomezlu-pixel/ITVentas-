<?php
require_once "../config/Conexion.php";

class Proveedor
{
    public function insertar($tipo_documento, $documento, $razon_social, $direccion, $telefono, $email, $contacto)
    {
        $sql = "INSERT INTO proveedor
        (tipo_documento, documento, razon_social, direccion, telefono, email, contacto)
        VALUES
        ('$tipo_documento', '$documento', '$razon_social', '$direccion', '$telefono', '$email', '$contacto')";
        return ejecutarConsulta($sql);
    }

    public function editar($idproveedor, $tipo_documento, $documento, $razon_social, $direccion, $telefono, $email, $contacto)
    {
        $sql = "UPDATE proveedor SET
        tipo_documento='$tipo_documento',
        documento='$documento',
        razon_social='$razon_social',
        direccion='$direccion',
        telefono='$telefono',
        email='$email',
        contacto='$contacto'
        WHERE idproveedor='$idproveedor'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idproveedor)
    {
        return ejecutarConsulta("UPDATE proveedor SET condicion=0 WHERE idproveedor='$idproveedor'");
    }

    public function listar()
    {
        return ejecutarConsulta("SELECT * FROM proveedor ORDER BY idproveedor DESC");
    }

    public function mostrar($idproveedor)
    {
        return ejecutarConsultaSimpleFila("SELECT * FROM proveedor WHERE idproveedor='$idproveedor'");
    }
}
?>
