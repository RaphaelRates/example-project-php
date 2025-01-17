<?php

namespace NodacWeb\Core;

use NodacWeb\Core\View;
use Exception;

abstract class Controller {

    /**
     * Metodo que retorna a view com base no nome da rota, no nome do arquivo da 
     * view e no array de dados
     *
     * @param string $view
     * @param string $page
     * @param array $data
     * @return void
     */
    public static function view(String $root,string $view,string $page, array $data = []) {
        $content = View::render($view, $data);
        return View::getPage($root,$page, $content);
    }

}
