<?php

namespace App\Http;

use Closure;
use Exception;
use ReflectionFunction;

class Router
{
    
    /**
     * raiz do projeto
     *
     * @var string
     */
    private $url = '';
        
    /**
     * prefixo da URL da rota
     *
     * @var string
     */
    private $prefix = '';
        
    /**
     * armazena as rotas
     *
     * @var array
     */
    private $routes = [];    

    /**
     * instancia de Request
     *
     * @var Request
     */
    private $request;
    
    /**
     * metodo construtor
     *
     * @param  string $url
     */
    public function __construct($url)
    {
        $this->request = new Request();
        $this->url = $url;
        $this->setPrefix();
    }
    
    /**
     * metodo para setar o prefixo da url
     *
     */
    private function setPrefix()
    {
        $parseUrl = parse_url($this->url);

        //DEFINE O PREFIXO
        $this->prefix = $parseUrl['path'] ?? '';
    }
    
    /**
     * metodo que adiciona uma rota na classe
     *
     * @param  string $method
     * @param  string $route
     * @param  array $params
     */
    private function addRoute($method, $route, $params = [])
    {
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        //VARIAVEIS DA ROTA
        $params['variables'] = [];

        //PADRAO DE VALIDAÇAO DAS VARIAVEIS DAS ROTAS
        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        //PADRAO DE VALIDAÇAO DA URL
        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        //ADICIONA A ROTA DENTRO DA CLASSE
        $this->routes[$patternRoute][$method] = $params;
    }
    
    /**
     * metodo que define uma rota GET
     *
     * @param  string $route
     * @param  array $params
     */
    public function get($route, $params = [])
    {
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * metodo que define uma rota POST
     *
     * @param  string $route
     * @param  array $params
     */
    public function post($route, $params = [])
    {
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * metodo que define uma rota PUT
     *
     * @param  string $route
     * @param  array $params
     */
    public function put($route, $params = [])
    {
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * metodo que define uma rota DELETE
     *
     * @param  string $route
     * @param  array $params
     */
    public function delete($route, $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);
    }
    
    /**
     * retorna URI sem o prefixo
     *
     * @return string
     */
    private function getUri()
    {
        //URI DA REQUEST
        $uri = $this->request->getUri();

        //FTIA A URI COM O PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
        
        //RETORNA A URI SEM PREFIXO
        return end($xUri);
    }
    
    /**
     * metodo que retorna os dados da rota atual
     *
     * @return array
     */
    private function getRoute()
    {
        //URI
        $uri = $this->getUri();

        //METODO DA REQUISIÇAO
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach ($this->routes as $patternRoute => $methods) {
            //VERIFICA SE A URI BATE COM O PADRAO
            if (preg_match($patternRoute, $uri, $matches)) {
                //VERIFICA O METODO
                if (array_key_exists($httpMethod, $methods)) {
                    //REMOVE A PRIMIRA POSIÇAO
                    unset($matches[0]);

                    //VARIAVEIS PROCESSADAS
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    //RETORNO DOS PARAMETROS DA ROTA
                    return $methods[$httpMethod];
                }
                //METODO NAO PERMITIDO/DEFINIDO
                throw new Exception("Method <em><b>" . array_keys($methods)[0] . "</b></em> not allowed here.", 405);
            }
        }
        //URL NAO ENCONTRADA
        throw new Exception("Page not found.", 404);
    }
    
    /**
     * metodo que executa a rota atual
     *
     * @return Response
     */
    public function run()
    {
        try {
            //OBTEM A ROTA ATUAL
            $route = $this->getRoute();
            
            //VERIFICA O CONTROLADOR
            if (!isset($route['controller'])) {
                throw new Exception("The current URL could not be processed.", 500);
            }

            //ARGUMENTOS DA FUNÇAO
            $args = [];

            //REFLECTION FUNCTION
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            //RETORNA A EXECUÇAO DA FUNÇAO
            return call_user_func_array($route['controller'], $args);

        } catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage());
        }
    }
}