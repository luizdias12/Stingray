<?php

namespace App\Http;

class Request
{
    
    /**
     * Metodo Http da requisiçao
     *
     * @var string
     */
    private $httpMethod;   

    /**
     * uri da pagina
     *
     * @var string
     */
    private $uri;

    /**
     * parametros do GET ($_GET)
     *
     * @var array
     */
    private $queryParams = []; 

    /**
     * variaveis recebidas via POST ($_POST)
     *
     * @var array
     */
    private $postVars = []; 

    /**
     * cabeçalho da requissiçao
     *
     * @var array
     */
    private $headers = [];
    
    /**
     * Construtor da classe
     *
     *
     */
    public function __construct()
    {
        $this->queryParams = $_GET ?? [];
        $this->postVars = $_POST ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';
    }
    
    /**
     * retorna o metodo HTTP
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }
    
    /**
     * retorna a Uri da pagina
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * retorna os headers da requisiçao
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * retorna os parametros da query string ($_GET)
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
    
    /**
     * retorna as variaveis enviadas via POST ($_POST)
     *
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }

}