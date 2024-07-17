<?php

use App\Http\Response;
use App\Controller\Pages;

$router->get('/',[
    function(){
        return new Response(200,Pages\Home::getHome());
    }
]);

$router->get('/about',[
    function(){
        return new Response(200,Pages\About::getAbout());
    }
]);

$router->get('/produto/{idPagina}/{acao}',[
    function($idPagina, $acao){
        return new Response(200,'Produto ' . $idPagina . ' - ' . $acao);
    }
]);