<?php

namespace NodacWeb\Core\Http\Middleware;

class BasicAuth{

    /**
     * Retorna uma instancia de usuario authrntuicado
     *
     * @return User
     */
    private function getBasicAuth(){
        if(!isset($_SERVER['PHP_AUTH_USER']) || isset($_SERVER['PHP_AUTH_PW'])){
            return false;
        }
        return true;
    }

    /**
     * Método respnsável por realizar o Auth
     *
     * @param [type] $request
     * @return void
     */
    private function basiAuth($request){
        if($obUser = $this->getBasicAuth()){

        }
    }

    /**
     * Método responsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request,$next){
        $this->basicAuth($request);
      return $next($request);
    }
}

