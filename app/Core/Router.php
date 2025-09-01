<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch($method, $uri) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                $params = $this->extractParams($route['path'], $uri);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // Route non trouvée
        http_response_code(404);
        $this->render404();
    }

    private function matchPath($routePath, $uri) {
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        return preg_match($pattern, $uri);
    }

    private function extractParams($routePath, $uri) {
        $params = [];
        $routeParts = explode('/', $routePath);
        $uriParts = explode('/', $uri);

        for ($i = 0; $i < count($routeParts); $i++) {
            if (isset($routeParts[$i]) && preg_match('/\{([^}]+)\}/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $uriParts[$i] ?? null;
            }
        }

        return $params;
    }

    private function callHandler($handler, $params = []) {
        list($controllerName, $method) = explode('@', $handler);

        $controllerClass = "App\\Controllers\\{$controllerName}";

        // L'autoloader se chargera de trouver et charger le fichier de la classe
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], array_values($params));
            } else {
                $this->render404();
            }
        } else {
            $this->render404();
        }
    }

    private function render404() {
        include '../app/Views/errors/404.php';
    }
}