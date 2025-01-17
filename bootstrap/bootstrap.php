<?php

namespace NodacWeb\Core;

use NodacWeb\Core\Http\Router;
use NodacWeb\Core\Http\Middleware\Queue;
use NodacWeb\Core\Http\Middleware\Maintenance;
use NodacWeb\Core\View;

define('URL', "http://localhost:8000");

class App {
    private $router;

    public function __construct() {
        $this->init();
        $this->router = new Router(URL);
    }

    // Método para inicializar as configurações do sistema
    private function init() {
        // Carrega configuração do sistema
        
        
        // Define middlewares padrão
        Queue::setMap([
            'maintenance' => Maintenance::class,
        ]);
        Queue::setDefault([
            'maintenance',
        ]);

        // Configurações iniciais das views
        View::init([
            'URL' => URL,
        ]);
    }

    // Método para retornar o roteador
    public function getRouter() {
        return $this->router;
    }

    public function run() {
        include __DIR__.'/../routes/routes.php';
        $this->router->run()->sendResponse();
    }
}
