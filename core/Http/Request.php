<?php
namespace NodacWeb\Core\Http;

class Request{
    /**
     * Metodo HTTP da requisição
     *
     * @var string
     */
    private $httpMethod;

    /**
     * Uri da página
     *
     * @var string
     */
    private $Uri;

    /**
     * Instãncia do router
     *
     * @var Request
     */
    private $router;

    /**
     * parâmetros da URL
     *
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas no Post da página ($_POST)
     *
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalhos da requisiçao
     *
     * @var array
     */
    private $headers = [];


    public function __construct($router) {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->setPostVars();
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
    }

    private function setPostVars(){
        if($this->httpMethod == 'GET') return false;
        $this->postVars = $_POST ?? [];
        $inputRaw = \file_get_contents('php://input');
        $this->postVars = (strlen($inputRaw) && empty($_POST))? json_decode($inputRaw, true) : $this->postVars;
    }

    /**
     * Método responsável por retornar a Instãncia do Router
     *
     * @return void
     */
    public function getRouter(){
        return $this->router;
    }

    /**
     * Método responsável por retornar o método HTTP da requisição 
     *
     * @return string
     */
    public function getHttpMethod(){
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar o URL da página 
     *
     * @return string
     */
    public function getUri(){
        return $this->Uri;
    }

    /**
     * Método responsável por definir o URL da página 
     *
     */
    public function setUri(){
        $this->Uri = $_SERVER['REQUEST_URI'] ?? '';
        $xUri = explode('?', $this->Uri);
        $this->Uri = $xUri[0];
    }

    /**
     * Método responsável por retornar os cabeçalhos da requisição
     *
     * @return array
     */
    public function getHeaders(){
        return $this->headers;
    }

    /**
     * Método responsável por retornar os parâmetros da URL de uma página
     *
     * @return array
     */
    public function getQueryParams(){
        return $this->queryParams;
    }

    /**
     * Método responsável por retornar as ariáveis POST da requisição
     *
     * @return array
     */
    public function getPostVars(){
        return $this->postVars;
    }

}
