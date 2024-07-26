<?php

namespace App\Utils;

class View {

    /**
     * variaveis padroes da view
     *
     * @var array
     */
    private static $vars = [];
    
    /**
     * define os dados iniciais da classe
     *
     * @param  array $vars
     */
    public static function init($vars = [])
    {
        self::$vars = $vars;
    }
    
    /**
     * retorna o conteudo de uma view
     *
     * @param  string $view
     * @return string
     */
    private static function getContentView($view)
    {
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }
    
    /**
     * retorna o conteudo renderiado de uma view
     *
     * @param  string $view
     * @param  array $vars
     * @return string
     */
    public static function render($view, $vars = [])
    {
        //CONTEUDO DA VIEW
        $contentView = self::getContentView($view);

        //MERGE DE VARIAVEIS DA VIEW
        $vars = array_merge(self::$vars, $vars);

        //CHAVES DO ARRAY DE VARIAVEIS
        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return '{{'.$item.'}}';
        }, $keys);

        //RETORNA O CONTEUDO RENDERIZADO
        return str_replace($keys, array_values($vars), $contentView);        
    }

}