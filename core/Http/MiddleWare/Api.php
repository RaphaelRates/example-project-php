<?php

namespace NodacWeb\Core\Http\Middleware;

class Api{

    /**
     * Método responsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request,$next){
        $request->getRouter()->setContentType('application/json');
      return $next($request);
    }
}

