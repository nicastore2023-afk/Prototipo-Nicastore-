<?php

declare(strict_types=1);

namespace Nicastore\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    
    public function get(string $path, callable|array $handler, array $middleware = []): self
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        return $this;
    }
    
    public function post(string $path, callable|array $handler, array $middleware = []): self
    {
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }
    
    public function put(string $path, callable|array $handler, array $middleware = []): self
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
        return $this;
    }
    
    public function delete(string $path, callable|array $handler, array $middleware = []): self
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
        return $this;
    }
    
    private function addRoute(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $pattern = $this->convertToRegex($path);
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                foreach ($route['middleware'] as $mw) {
                    if (is_callable($mw)) {
                        $result = $mw();
                        if ($result === false) {
                            return;
                        }
                    }
                }
                
                $this->callHandler($route['handler'], $params);
                return;
            }
        }
        
        $this->notFound();
    }
    
    private function callHandler(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            if (is_string($controller)) {
                $controller = new $controller();
            }
            call_user_func_array([$controller, $method], $params);
        } else {
            call_user_func_array($handler, $params);
        }
    }
    
    private function notFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../../src/Views/errors/404.php';
    }
    
    public function getRoutes(): array
    {
        return $this->routes;
    }
}