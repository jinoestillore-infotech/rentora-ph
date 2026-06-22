<?php

namespace App\Core;

class Router {
    private array $routes = [];

    /**
     * Register a GET route.
     * * @param string $path
     * @param array $callback [ControllerClass, 'methodName']
     */
    public function get(string $path, array $callback): void {
        $this->routes['GET'][$this->normalizePath($path)] = $callback;
    }

    /**
     * Register a POST route.
     * * @param string $path
     * @param array $callback [ControllerClass, 'methodName']
     */
    public function post(string $path, array $callback): void {
        $this->routes['POST'][$this->normalizePath($path)] = $callback;
    }

    /**
     * Resolves the incoming URL request against registered routes.
     */
    public function resolve(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->normalizePath($_SERVER['REQUEST_URI']);

        // Remove query strings from path lookup
        $path = explode('?', $path)[0];

        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            $controllerClass = $callback[0];
            $methodName = $callback[1];

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                    return;
                }
            }
        }

        // Return standard 404 response
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1><p>The page you are looking for does not exist on RENTORA PH.</p>";
    }

    /**
     * Normalizes a path to match patterns.
     */
    private function normalizePath(string $path): string {
        $path = trim($path, '/');
        return $path === '' ? '/' : '/' . $path;
    }
}