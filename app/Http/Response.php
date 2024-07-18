<?php

namespace App\Http;

class Response {
    
    /**
     * o codigo do status Http da resposta
     *
     * @var int
     */
    private $httpCode = 200;    

    /**
     * cabeçalho do Response
     *
     * @var array
     */
    private $headers = [];
        
    /**
     * o tipo de conteudo do Response
     *
     * @var string
     */
    private $contentType = 'text/html';
    
    /**
     * o conteudo do Response
     *
     * @var mixed
     */
    private $content;
    
    /**
     * o metodo construtor
     *
     * @param  int $httpCode
     * @param  mixed $content
     * @param  string $contentType
     * 
     */
    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }
    
    /**
     * altera o Content-Type do Response
     *
     * @param  string $contentType
     * 
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }
    
    /**
     * adiciona um registro no cabeçalho de Response
     *
     * @param  string $key
     * @param  string $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
    
    /**
     * envia os headers para o navegador
     *
     */
    private function sendHeaders()
    {
        //STATUS
        http_response_code($this->httpCode);

        //ENVIAR HEADERS
        foreach($this->headers as $key => $value)
        {
            header($key.': '.$value);
        }
    }
    
    /**
     * envia a resposta para o usuario
     *
     */
    public function sendResponse()
    {
        //ENVIA OS HEADERS
        $this->sendHeaders();

        //IMPRIME O CONTEUDO
        switch($this->contentType){
            case 'text/html':
                echo $this->content;
                exit;
        }
    }
}