<?php

use NodacWeb\Controllers\HomeController;
use NodacWeb\Core\Http\Response;

// Acessa a instância do roteador já configurado
$objRouter = $this->getRouter();

// Rotas definidas
$objRouter->get("/", [
    function($request) {
        return new Response(200, HomeController::index($request));
    }
]);

$objRouter->get("/teste", [
    function($request) {
        return new Response(200, HomeController::testePython($request));
    }
]);

$objRouter->post("/", [
    function($request) {
        return new Response(200, HomeController::create($request));
    }
]);



$objRouter->get("/about", [
    function() {
        return new Response(200, HomeController::about());
    }
]);

$objRouter->get("/about/{number}/{acao}", [
    function($number, $acao = 'nada') {
        return new Response(200, HomeController::about($number, $acao));
    }
]);

$objRouter->get("/about/{number}", [
    function($number) {
        return new Response(200, HomeController::about($number));
    }
]);

$objRouter->get("/contato", [
    function($number) {
        return new Response(200, HomeController::contact());
    }
]);

$objRouter->get("/{id}/edit", [
    function($request, $id) {
        return new Response(200, HomeController::editUser($request,$id));
    }
]);

$objRouter->post("/{id}/edit", [
    function($request, $id) {
        return new Response(200, HomeController::updateUser($request,$id));
    }
]);
$objRouter->get("/{id}/delete", [
    function($request, $id) {
        return new Response(200, HomeController::deleteUser($request,$id));
    }
]);

$objRouter->get("/{id}", [
    function($request, $id) {
        return new Response(200, HomeController::getSingleUSer($request,$id));
    }
]);


