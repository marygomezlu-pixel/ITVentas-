<?php
require_once __DIR__ . "/seguridad.php";
exigirSesion();
require_once "../modelos/Venta.php";

$venta = new Venta();
$op = $_GET["op"] ?? "";

header("Content-Type: application/json; charset=utf-8");

switch ($op) {
    case "articulos":
        $rspta = $venta->articulos();
        $data = [];
        while ($reg = $rspta->fetch_assoc()) {
            $data[] = $reg;
        }
        echo json_encode(["data" => $data]);
        break;

    case "listar":
        $rspta = $venta->listar();
        $data = [];
        while ($reg = $rspta->fetch_assoc()) {
            $data[] = $reg;
        }
        echo json_encode(["data" => $data]);
        break;

    case "registrar":
        $detalles = json_decode($_POST["detalles"] ?? "[]", true);

        $mensaje = $venta->registrar(
            trim($_POST["tipo_comprobante"] ?? "Boleta"),
            trim($_POST["serie"] ?? ""),
            trim($_POST["correlativo"] ?? ""),
            trim($_POST["observacion"] ?? ""),
            $detalles
        );

        echo json_encode(["mensaje" => $mensaje]);
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
?>
