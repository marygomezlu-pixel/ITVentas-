<?php
require_once "../config/Conexion.php";

class Cliente
{
    public function insertar($tipo_documento, $documento, $nombre, $telefono, $email, $direccion)
    {
        $sql = "INSERT INTO cliente
        (tipo_documento, documento, nombre, telefono, email, direccion)
        VALUES
        ('$tipo_documento', '$documento', '$nombre', '$telefono', '$email', '$direccion')";
        return ejecutarConsulta($sql);
    }

    public function editar($idcliente, $tipo_documento, $documento, $nombre, $telefono, $email, $direccion)
    {
        $sql = "UPDATE cliente SET
        tipo_documento='$tipo_documento',
        documento='$documento',
        nombre='$nombre',
        telefono='$telefono',
        email='$email',
        direccion='$direccion'
        WHERE idcliente='$idcliente'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idcliente)
    {
        return ejecutarConsulta("UPDATE cliente SET condicion=0 WHERE idcliente='$idcliente'");
    }

    public function listar()
    {
        return ejecutarConsulta("SELECT * FROM cliente ORDER BY idcliente DESC");
    }

    public function mostrar($idcliente)
    {
        return ejecutarConsultaSimpleFila("SELECT * FROM cliente WHERE idcliente='$idcliente'");
    }
}
?>