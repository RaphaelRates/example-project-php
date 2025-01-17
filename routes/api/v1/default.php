<?php

use NodacWeb\Core\Http\Response;

$objRouter->get('/api/v1',[
    function(){
        return new Response(200, 'API:', 'application/json');
    }
]);