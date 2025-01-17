<?php

namespace NodacWeb\Core\Http;

include __DIR__.'/../helpers/Helper.php';

use NodacWeb\Core\Http\Request;
use NodacWeb\Core\Http\Response;
use NodacWeb\Core\Http\Middleware\Queue as MiddlewareQueue;

use \Exception;
use \Closure;
use \ReflectionFunction;


class Router {
    /**
     * Índice de rotas
     *
     * @var array
     */
    private $routes = [];

    /**
     * Url completa do projeto (raiz)
     *
     * @var string
     */
    private $url = '';

    /**
     * Prefixo de todas as rotas
     *
     * @var string
     */
    private $prefix = '';

    /**
     * Instância do Request
     *
     * @var Request
     */
    private $request;

    /**
     * ContentType padrão do response
     *
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Construtor da classe
     *
     * @param string $url
     */
    public function __construct($url) {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por retornar a URI desconsiderando o prefixo
     *
     * @return string
     */
    private function getUri(){
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix,$uri): [$uri];
        return end($xUri);
    }

    /**
     * Método responsável por retornar a URI desconsiderando o prefixo
     *
     * @return void
     */
    public function setContentType($contentType){
        $this->contentType = $contentType;
    }

     /**
     * Método responsável pos definir o prfixo das rotas
     *
     * @return void
     */
    private function setPrefix(){
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';

    }

        /**
 * Método responsável por adicionar uma rota na classe
 *
 * @param string $method
 * @param string $route
 * @param array $params
 * @return void
 */
private function addRoute($method, $route,$params = []){
    foreach ($params as $key => $value) {
        if($value instanceof Closure){
            $params['controller'] = $value;
            unset($params[$key]);
            continue;
        }
    }
    $params['middlewares'] = $params['middlewares'] ?? []; 
    $params['variables'] = [];
    $patternVariable = '/{(.*?)}/';
    if(preg_match_all($patternVariable, $route, $matches)){
        $route = preg_replace($patternVariable,'(.*?)', $route);
        $params['variables'] = $matches[1];
    }
    $patternRoute = '/^'.str_replace('/','\/',$route).'$/';
    $this->routes[$patternRoute][$method] = $params;

}


    /**
     * Método responsável por retornar os dados da rota atual
     *
     * @return void
     */
    private function getRoute() {
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();
    
        foreach ($this->routes as $patternRoute => $method) {

            if (preg_match($patternRoute, $uri, $matches)) {
                if (isset($method[$httpMethod])) {
                    unset($matches[0]);
                    $keys = $method[$httpMethod]['variables'];
                    $method[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $method[$httpMethod]['variables']['request'] = $this->request;
                    return $method[$httpMethod];
                }
                throw new Exception("Método não permitido", 405);
            }
        }
        throw new Exception("URL não encontrada", 404);
    }
    

    private function getErrorMessage($message){
        switch ($this->contentType) {
            case 'application/json':
                return [
                    'error' => $message
                ];
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * Método responsável por definir uma rota GET
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir uma rota POST
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Método responsável por definir uma rota PUT
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por definir uma rota DELETE
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }

 /**
 * Método responsável por redirecionar a página com suporte a múltiplas funcionalidades
 *
 * @param string $router 
 * @param array $queryParams 
 * @param array $flashMessage 
 * @param int $statusCode 
 * @param bool $isAbsolute 
 * @return void
 */
public function redirect(string $router,array $queryParams = [],array $flashMessage = [],int $statusCode = 302,bool $isAbsolute = false
) {
    $url = $isAbsolute ? $router : $this->url . $router;
    if (!empty($queryParams)) {
        $queryString = http_build_query($queryParams);
        $url .= '?' . $queryString;
    }
    if (!empty($flashMessage)) {
        $_SESSION['flash'] = $flashMessage;
    }
    error_log("Redirecionamento para: $url com status $statusCode");
    http_response_code($statusCode);
    header('Location: ' . $url);
    exit;
}


   /**
    * Método responsável por carregar as rotas através dos Controllers, Middlewares e Variables
    *
    * @return MiddlewareQueue
    */
    public function run(){
        try {
            $route = $this->getRoute();

            if(!isset($route['controller'])){
                throw new Exception("URL nã pôde ser processada",500);
            }
            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameters) {
                $name = $parameters->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);

            // return call_user_func_array($route['controller'], $args);
        } catch (Exception $e) {
            return new Response($e->getCode(),$this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }
}


?>