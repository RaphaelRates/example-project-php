<?php

namespace NodacWeb\Core;

use Exception;

class View {

    /**
     * Variáveis padõres da View
     *
     * @var array
     */
    private static $vars = [];

    public static function init($vars = []){
        self::$vars = $vars;
    }

    /**
     * Retorna o conteudp da pagina
     *
     * @param string $view
     * @return text/html
     */
    private static function getContentView($view) {
        $phpFile = dirname(__FILE__, 2) . "/app/views/" . $view . ".php";
        $htmlFile = dirname(__FILE__, 2) . "/app/views/" . $view . ".html";
        if (file_exists($phpFile)) {
            ob_start();
            include $phpFile; 
            return ob_get_clean(); 
        } elseif (file_exists($htmlFile)) {
            return file_get_contents($htmlFile); 
        } else {
            return "<p>View '{$view}' não encontrada.</p>";
        }
    }
    

    /** 
     * Renderiza a view de acordo com o seu nome e com os dados repassados
     * verificando a existência da view e depois extraindo os dados
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function render(string $view, array $data = []) {
        $content = self::getContentView($view);
        $data = array_merge(self::$vars,$data);
        $keys = array_keys($data);
        $keys = array_map(function($item) {
            return '{{' . $item . '}}';
        }, $keys);
        return str_replace($keys, array_values($data), $content);
    } 

    /**
     * Retorna o header padrão utilizado, essa função pode ser modificada se necessário
     * ou ter uma confuncionalidade parecida
     * @return string
     */
    public static function getHeader($root){
        switch ($root) {
            case 'rootAdmin':
                return self::render('components/headerAdmin', []);
            case 'rootUser':
                return self::render('components/header', []);
            case 'rootJuror':
                return self::render('components/header', []);
            default:
                return self::render('components/header', []);
                break;
        }
    }

    /**
     * Retorna o Footer padrõa utilizado, essa função pode ser modificada se necessário
     * ou ter ima com a funcionalidade parecida
     * @return void
     */
    public static function getFooter($root){
        switch ($root) {
            case 'rootAdmin':
                return self::render('components/footer', []);
            case 'rootUser':
                return self::render('components/footer', []);
            case 'rootJuror':
                return self::render('components/footer', []);
            default:
                return self::render('components/footer', []);
                break;
        }
    }


    /**
     * Método responsável por retornar a página principal de acordo com a raíz e com 
     * o título e e o conteudo
     *
     * @param [type] $root
     * @param [type] $title
     * @param [type] $content
     * @return void
     */
    public static function getPage($root,$title, $content){
        return self::render($root,[
            'title' => $title,
            'header' => self::getHeader($root),
            'content' => $content,
            'footer' => self::getFooter($root),
        ]);
    }

   /**
     * Método responsável por retornar a página principal de acordo com a raíz e com 
     * o título e e o conteudo
     *
     * @param [type] $root
     * @param [type] $title
     * @param [type] $content
     * @return void
     */
    public static function renderPagination($currentPage, $totalPages) {
        $linkers = "";
        if ($currentPage > 1) {
            $linkers .= self::render("components/pagination/link",[
                "text" => "<<",
                "page" => 1,
                "active" => "linker"
            ]);
            $linkers .= self::render("components/pagination/link",[
                "text" => "<",
                "page" => $currentPage - 1,
                "active" => "linker",
            ]);
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            $linkers .= self::render("components/pagination/link",[
                "text" => $i,
                "page" => $i,
                "active" => ($i == $currentPage)? "linker--active" : "linker",
            ]);
        }
        if ($currentPage < $totalPages) {
            $linkers .= self::render("components/pagination/link",[
                "text" => ">",
                "page" => $currentPage + 1,
                "active" => "linker",
            ]);
            $linkers .= self::render("components/pagination/link",[
                "text" => ">>",
                "page" => $totalPages,
                "active" => "linker",
            ]);
        }
        return self::render("components/pagination/linkers", [
            "linkers" => $linkers
        ]);
    }    
}
?>