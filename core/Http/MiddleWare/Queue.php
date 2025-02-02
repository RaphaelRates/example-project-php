<?php

namespace NodacWeb\Core\Http\Middleware;

use \Closure;
use NodacWeb\Core\Http\Middleware\Maintenance;

class Queue{

    /**
     * Mapeamento de middlewares
     *
     * @var array
     */
    private static $map = [];

    /**
     * Mapeamento de middlewares que serão carregados em todas as rotas
     *
     * @var array
     */
    private static $default = [];
    /**
     * Fila de middlewares a serem executados;
     *
     * @var array
     */
    private $middlewares = [];

    /**
     * Função de execuçã do controller
     *
     * @var Closure
     */
    private $controller;

    /**
     * Argumentos de função do controller
     *
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Método construtor da Queue de middlewares
     *
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct(array $middlewares, Closure $controller,array $controllerArgs) {
        $this->middlewares = array_merge(self::$default ,$middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;

    }

    /**
     * Método responsável por definir o mapeamento de middlewares
     *
     * @param array $map
     * @return void
     */
    public static function setMap($map){
        self::$map = $map;
    }

    /**
     * Método responsável por definir o mapeamento de middlewares
     *
     * @param array $map
     * @return void
     */
    public static function setDefault($default){
        self::$default = $default;
    }

    /**
     * Método responsável por executar o próximo nível da fila de middlewares
     *
     * @param Request $request
     * @return Response
     */
    public function next($request){
        if(empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);
        $middleware = array_shift($this->middlewares);
        if(!isset(self::$map[$middleware])) throw new \Exception("Problemas ao processar o middlesware da requisição",500);
        $queue = $this;
        $next = function($request) use($queue){ return $queue->next($request);};
        return (new self::$map[$middleware])->handle($request,$next);
    }

}
