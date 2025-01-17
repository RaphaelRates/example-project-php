<?php

use NodacWeb\Controllers\HomeController;
use NodacWeb\Core\Http\Response;

// Acessa a instância do roteador já configurado
$objRouter = $this->getRouter();

// Rotas do Administrador definidas
$objRouter->get("/admin", [
    'middlewares' => [
        'maintenance',
    ],
    function($request) {
        return new Response(200, HomeController::index($request));
    }
]);