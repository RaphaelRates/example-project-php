<?php
namespace NodacWeb\Core\Http;

class Response{
    /**
     * Código de Status HTTP
     *
     * @var integer
     */
    private $httpCode = 200;

    /**
     * Tipo de dado que está sendo retornado
     *
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo do response
     *
     * @var mixed
     */
    private $content = [];

    /**
     * Cabeçalhos da requisiçao
     *
     * @var array
     */
    private $headers = [];


    public function __construct($httpCode, $content,$contentType = 'text/html') {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }

    /**
     * Método responsável por modificar co Content/Type do Response 
     *
     * @return string
     */
    public function setContentType($contentType){
        $this->contentType = $contentType;
        $this->addHeader("Content-Type",$contentType);
    }

    /**
     * Método responsável adicionar um cabeçalho no Response
     *
     * @return string
     */
    public function addHeader($key, $value){
        $this->headers[$key] = $value;
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
     * Método responsável por definir os cabeçalhos
     *
     * @return void
     */
    private function sendHeaders(){
        http_response_code($this->httpCode);
        foreach ($this->headers as $key => $value) {
            header($key.': '.$value);
        }
    }

    /**
     * Método responsável por enviar o Response
     *
     * @return void
     */
    public function sendResponse() {
        $this->sendHeaders();
        
        // Definindo os tipos de conteúdo com base no valor de $this->contentType
        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                break;
            case 'text/php':
                echo $this->content;
                break;
            case 'application/json':
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                break;
            case 'text/css':
                header('Content-Type: text/css; charset=UTF-8');
                echo $this->content; 
                break;
            case 'application/javascript':
            case 'text/javascript':
                header('Content-Type: application/javascript; charset=UTF-8');
                echo $this->content;
                break;
            default:
                echo $this->content;
                break;
        }
    }
    
}
?>