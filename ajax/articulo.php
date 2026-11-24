<?php
require_once __DIR__ . "/seguridad.php";
exigirSesion();
require_once "../modelos/Articulo.php";
require_once "../modelos/Categoria.php";

$articulo = new Articulo();
$categoria = new Categoria();

$idarticulo = isset($_POST["idarticulo"]) ? limpiarCadena($_POST["idarticulo"]) : "";
$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$stock = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "0";
$precio_compra = isset($_POST["precio_compra"]) ? limpiarCadena($_POST["precio_compra"]) : "0";
$precio_venta = isset($_POST["precio_venta"]) ? limpiarCadena($_POST["precio_venta"]) : "0";

switch ($_GET["op"] ?? "") {

    case "guardaryeditar":
        if (empty($idarticulo)) {
            $respuesta = $articulo->insertar($idcategoria, $codigo, $nombre, $descripcion, $stock, $precio_compra, $precio_venta);
            echo $respuesta ? "ArtÃ­culo registrado correctamente" : "No se pudo registrar el artÃ­culo";
        } else {
            $respuesta = $articulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $descripcion, $stock, $precio_compra, $precio_venta);
            echo $respuesta ? "ArtÃ­culo actualizado correctamente" : "No se pudo actualizar el artÃ­culo";
        }
        break;

    case "desactivar":
        $respuesta = $articulo->desactivar($idarticulo);
        echo $respuesta ? "ArtÃ­culo desactivado" : "No se pudo desactivar";
        break;

    case "activar":
        $respuesta = $articulo->activar($idarticulo);
        echo $respuesta ? "ArtÃ­culo activado" : "No se pudo activar";
        break;

    case "mostrar":
        $respuesta = $articulo->mostrar($idarticulo);
        echo json_encode($respuesta);
        break;

    case "listar":
        $rspta = $articulo->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "idarticulo" => $reg->idarticulo,
                "idcategoria" => $reg->idcategoria,
                "categoria" => $reg->categoria,
                "codigo" => $reg->codigo,
                "nombre" => $reg->nombre,
                "descripcion" => $reg->descripcion,
                "stock" => $reg->stock,
                "precio_compra" => $reg->precio_compra,
                "precio_venta" => $reg->precio_venta,
                "condicion" => $reg->condicion
            ];
        }

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(["data" => $data]);
        break;

    case "selectCategoria":
        $rspta = $categoria->select();
        echo '<option value="">Seleccione una categorÃ­a</option>';

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->idcategoria . '">' .
                htmlspecialchars($reg->nombre, ENT_QUOTES, "UTF-8") .
                '</option>';
        }
        break;
}
