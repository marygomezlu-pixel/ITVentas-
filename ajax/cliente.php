<?php
require_once __DIR__ . "/seguridad.php";
exigirSesion();
require_once "../modelos/Cliente.php";

$cliente = new Cliente();

$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "DNI";
$documento = isset($_POST["documento"]) ? limpiarCadena($_POST["documento"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";

switch ($_GET["op"] ?? "") {
    case "guardaryeditar":
        if (empty($idcliente)) {
            $respuesta = $cliente->insertar($tipo_documento, $documento, $nombre, $telefono, $email, $direccion);
            echo $respuesta ? "Cliente registrado correctamente" : "No se pudo registrar el cliente. Verifica que el documento no esté duplicado.";
        } else {
            $respuesta = $cliente->editar($idcliente, $tipo_documento, $documento, $nombre, $telefono, $email, $direccion);
            echo $respuesta ? "Cliente actualizado correctamente" : "No se pudo actualizar el cliente. Verifica que el documento no esté duplicado.";
        }
        break;

    case "desactivar":
        $respuesta = $cliente->desactivar($idcliente);
        echo $respuesta ? "Cliente desactivado correctamente" : "No se pudo desactivar el cliente";
        break;

    case "listar":
        header("Content-Type: application/json; charset=utf-8");
        $rspta = $cliente->listar();
        $data = [];
        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "idcliente" => $reg->idcliente,
                "tipo_documento" => $reg->tipo_documento,
                "documento" => $reg->documento,
                "nombre" => $reg->nombre,
                "telefono" => $reg->telefono,
                "email" => $reg->email,
                "direccion" => $reg->direccion,
                "condicion" => $reg->condicion
            ];
        }
        echo json_encode(["data" => $data], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo "Operación no válida";
}
?>