<?php
require_once "../config/Conexion.php";

class Usuario
{
    public function roles()
    {
        global $conexion;

        return $conexion->query(
            "SELECT idrol, nombre
             FROM rol
             WHERE condicion = 1
             ORDER BY nombre"
        );
    }

    public function listar()
    {
        global $conexion;

        return $conexion->query(
            "SELECT u.idusuario, u.idrol, u.nombre,
                    u.tipo_documento, u.documento,
                    u.email, u.login, u.condicion,
                    r.nombre AS perfil
             FROM usuario u
             INNER JOIN rol r ON u.idrol = r.idrol
             ORDER BY u.idusuario DESC"
        );
    }

    public function mostrar($idusuario)
    {
        global $conexion;

        $stmt = $conexion->prepare(
            "SELECT idusuario, idrol, nombre, tipo_documento,
                    documento, email, login, condicion
             FROM usuario
             WHERE idusuario = ?"
        );

        $stmt->bind_param("i", $idusuario);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function registrar($idrol, $nombre, $tipo_documento,
                              $documento, $email, $login, $clave)
    {
        global $conexion;

        if ($idrol <= 0 || $nombre === "" || $login === "" || $clave === "") {
            return "Completa los campos obligatorios";
        }

        $hash = password_hash($clave, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare(
            "INSERT INTO usuario
            (idrol, nombre, tipo_documento, documento, email, login, clave)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issssss",
            $idrol,
            $nombre,
            $tipo_documento,
            $documento,
            $email,
            $login,
            $hash
        );

        if ($stmt->execute()) {
            return "Usuario registrado correctamente";
        }

        if ($stmt->errno == 1062) {
            return "El correo o nombre de usuario ya está registrado";
        }

        return "No se pudo registrar el usuario";
    }

    public function editar($idusuario, $idrol, $nombre,
                           $tipo_documento, $documento,
                           $email, $login, $clave = "")
    {
        global $conexion;

        if ($idusuario <= 0 || $idrol <= 0 || $nombre === "" || $login === "") {
            return "Completa los campos obligatorios";
        }

        if ($clave !== "") {
            $hash = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $conexion->prepare(
                "UPDATE usuario
                 SET idrol=?, nombre=?, tipo_documento=?,
                     documento=?, email=?, login=?, clave=?
                 WHERE idusuario=?"
            );

            $stmt->bind_param(
                "issssssi",
                $idrol,
                $nombre,
                $tipo_documento,
                $documento,
                $email,
                $login,
                $hash,
                $idusuario
            );
        } else {
            $stmt = $conexion->prepare(
                "UPDATE usuario
                 SET idrol=?, nombre=?, tipo_documento=?,
                     documento=?, email=?, login=?
                 WHERE idusuario=?"
            );

            $stmt->bind_param(
                "isssssi",
                $idrol,
                $nombre,
                $tipo_documento,
                $documento,
                $email,
                $login,
                $idusuario
            );
        }

        if ($stmt->execute()) {
            return "Usuario actualizado correctamente";
        }

        if ($stmt->errno == 1062) {
            return "El correo o nombre de usuario ya está registrado";
        }

        return "No se pudo actualizar el usuario";
    }

    public function cambiarEstado($idusuario, $condicion)
    {
        global $conexion;

        $stmt = $conexion->prepare(
            "UPDATE usuario SET condicion=? WHERE idusuario=?"
        );

        $stmt->bind_param("ii", $condicion, $idusuario);

        if ($stmt->execute()) {
            return $condicion == 1
                ? "Usuario activado correctamente"
                : "Usuario desactivado correctamente";
        }

        return "No se pudo cambiar el estado del usuario";
    }
}
?>
