<?php

namespace App\Http;

use Closure;
use Exception;
use ReflectionFunction;

class Router
{

    private $url = '';
    private $prefix = '';
    private $routes = [];
    private $request;

    public function __construct($url)
    {
        $this->request = new Request();
        $this->url = $url;
        $this->setPrefix();
    }

    private function setPrefix()
    {
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';
    }

    private function addRoute($method, $route, $params = [])
    {
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        $params['variables'] = [];

        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        $this->routes[$patternRoute][$method] = $params;

        // echo "<pre>";
        // print_r($this);
        // echo "</pre>";
    }

    public function get($route, $params = [])
    {
        return $this->addRoute('GET', $route, $params);
    }

    public function post($route, $params = [])
    {
        return $this->addRoute('POST', $route, $params);
    }

    public function put($route, $params = [])
    {
        return $this->addRoute('PUT', $route, $params);
    }

    public function delete($route, $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);
    }

    private function getUri()
    {
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
        return end($xUri);
    }

    private function getRoute()
    {
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();

        foreach ($this->routes as $patternRoute => $methods) {
            if (preg_match($patternRoute, $uri, $matches)) {
                // echo "<pre>";
                // print_r($matches);
                // echo "</pre>";
                // exit;
                if (isset($httpMethod, $methods)) {

                    unset($matches[0]);

                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }

                throw new Exception("Method <em><b>" . array_keys($methods)[0] . "</b></em> not allowed here.", 405);
            }
        }

        throw new Exception("Page not found.", 404);
    }

    public function run()
    {
        try {
            $route = $this->getRoute();
            // echo "<pre>";
            // print_r($route);
            // echo "</pre>";
            // exit;
            if (!isset($route['controller']))
            {
                throw new Exception("The current URL could not be processed.", 500);
            }

            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter)
            {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            // echo "<pre>";
            // print_r($route['variables'][$name]);
            // echo "</pre>";
            // exit;

            return call_user_func_array($route['controller'], $args);

        } catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage());
        }
    }
}