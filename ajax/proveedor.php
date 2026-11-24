<?php
require_once __DIR__ . "/seguridad.php";
exigirSesion();
require_once "../modelos/Proveedor.php";

$proveedor = new Proveedor();

$idproveedor = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "RUC";
$documento = isset($_POST["documento"]) ? limpiarCadena($_POST["documento"]) : "";
$razon_social = isset($_POST["razon_social"]) ? limpiarCadena($_POST["razon_social"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$contacto = isset($_POST["contacto"]) ? limpiarCadena($_POST["contacto"]) : "";

switch ($_GET["op"] ?? "") {
    case "guardaryeditar":
        if (empty($idproveedor)) {
            $respuesta = $proveedor->insertar($tipo_documento, $documento, $razon_social, $direccion, $telefono, $email, $contacto);
            echo $respuesta ? "Proveedor registrado correctamente" : "No se pudo registrar el proveedor";
        } else {
            $respuesta = $proveedor->editar($idproveedor, $tipo_documento, $documento, $razon_social, $direccion, $telefono, $email, $contacto);
            echo $respuesta ? "Proveedor actualizado correctamente" : "No se pudo actualizar el proveedor";
        }
        break;

    case "desactivar":
        $respuesta = $proveedor->desactivar($idproveedor);
        echo $respuesta ? "Proveedor desactivado correctamente" : "No se pudo desactivar el proveedor";
        break;

    case "mostrar":
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($proveedor->mostrar($idproveedor));
        break;

    case "listar":
        header("Content-Type: application/json; charset=utf-8");
        $rspta = $proveedor->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "idproveedor" => $reg->idproveedor,
                "tipo_documento" => $reg->tipo_documento,
                "documento" => $reg->documento,
                "razon_social" => $reg->razon_social,
                "direccion" => $reg->direccion,
                "telefono" => $reg->telefono,
                "email" => $reg->email,
                "contacto" => $reg->contacto,
                "condicion" => $reg->condicion
            ];
        }

        echo json_encode(["data" => $data]);
        break;
}
?>
