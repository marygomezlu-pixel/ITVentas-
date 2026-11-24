<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function exigirSesion() {
    if (empty($_SESSION["itventas_usuario"])) {
        http_response_code(401);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "ok" => false,
            "mensaje" => "Debes iniciar sesión"
        ]);
        exit;
    }
}

function exigirAdmin() {
    exigirSesion();

    if ((int)($_SESSION["itventas_usuario"]["idrol"] ?? 0) !== 1) {
        http_response_code(403);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "ok" => false,
            "mensaje" => "No tienes permisos de administrador"
        ]);
        exit;
    }
}