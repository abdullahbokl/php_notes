<?php

class Router {
    private array $routes;

    public function __construct(array $routes) {
        $this->routes = $routes;
    }

    public function handleRequest(): void {
        // Remove the script name (index.php) from the request URI
        $request_uri = str_replace('/index.php', '', strtok($_SERVER['REQUEST_URI'], '?'));

        if (isset($this->routes[$request_uri])) {
            $method = $_SERVER['REQUEST_METHOD'];
            if (isset($this->routes[$request_uri][$method])) {
                include $this->routes[$request_uri][$method];
            } else {
                HelperMethods::sendResponse(null, 'Method not allowed', 405);
            }
        } else {
            HelperMethods::sendResponse(null, 'Route not found', 404);
        }
    }
}