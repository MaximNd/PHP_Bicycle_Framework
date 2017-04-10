<?php

final class Router {

    protected static $routes = [];
    protected static $route = [];

    public static function add($regexp, $route = []) {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes() {
        return self::$routes;
    }

    public static function getRoute() {
        return self::$route;
    }

    private static function matchRoute($url) {
        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#$pattern#i", $url, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $route[$key] = $value;
                    }
                }
                if (!isset($route['action'])) {
                    $route['action'] = 'index';
                }
                self::$route = $route;
                return true;
            }
        }
        return false;
    }

    /**
     * перенапрвляет URL по корректному маршруту
     * @param string $url входящий URL
     * @return void
     */
    public static function dispatch($url) {
        if (self::matchRoute($url)) {
            $controller = self::upperCamelCase(self::$route['controller']);

            //self::upperCamelCase($controller);
            if (class_exists($controller)) {
                $controllerObj = new $controller;
                $action = self::lowerCamelCase(self::$route['action']) . "Action";
                if (method_exists($controllerObj, $action)) {
                    $controllerObj->$action();
                } else {
                    echo "Method $controller::$action NOT FOUND!!!!";
                }
            } else {
                echo "Conroller $controller NOT FOUND!!!!";
            }
        } else {
            http_response_code(404);
            include '404.html';
        }
    }

    protected static function upperCamelCase($name) {
        $name = str_replace('-', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);
        return $name;
    }

    protected static function lowerCamelCase($name) {
        return lcfirst(self::upperCamelCase($name));
    }

}
