<?php
/**
 * Standalone utilities to replace Composer dependencies
 */

/**
 * Simple Router to replace AltoRouter
 */
class SimpleRouter {
    private $routes = [];
    private $basePath = '';

    public function setBasePath($path) {
        $this->basePath = rtrim($path, '/');
    }

    public function map($method, $route, $target, $name = null) {
        $this->routes[] = [
            'method' => $method,
            'route'  => $route,
            'target' => $target,
            'name'   => $name
        ];
    }

    public function match() {
        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($this->basePath && strpos($requestUrl, $this->basePath) === 0) {
            $requestUrl = substr($requestUrl, strlen($this->basePath));
        }
        $requestUrl = '/' . trim($requestUrl, '/');
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod && $route['method'] !== 'MATCH') continue;

            $pattern = $route['route'];
            // Handle optional segments like /[*:slug]?
            $pattern = preg_replace('#/\[\*:([a-zA-Z0-9_]+)\]\?#', '(?:/(?P<$1>.*))?', $pattern);
            // Handle required segments: [a:param] accepte lettres (min/maj) et chiffres
            $pattern = preg_replace('/\[[a-zA-Z]:([a-zA-Z0-9_]+)\]/', '(?P<$1>[^/]+)', $pattern);
            
            $pattern = '#^' . $pattern . '/?$#';

            if (preg_match($pattern, $requestUrl, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return [
                    'target' => $route['target'],
                    'params' => $params,
                    'name'   => $route['name']
                ];
            }
        }
        return false;
    }

    public function generate($name, $params = []) {
        foreach ($this->routes as $route) {
            if ($route['name'] === $name) {
                $url = $route['route'];
                foreach ($params as $key => $value) {
                    $url = preg_replace('/\[[a-zA-Z]:' . preg_quote($key, '/') . '\]/', $value, $url);
                }
                return (defined('SITE_URL') ? SITE_URL : '') . $url;
            }
        }
        return '#';
    }
}

/**
 * Simple Hashids-like implementation
 */
class SimpleHashids {
    private $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
    private $salt = 'DSM_DEFAULT_SALT';
    private $minLength = 8;

    public function __construct($salt = '', $minLength = 0, $alphabet = '') {
        if ($salt) $this->salt = $salt;
        if ($minLength) $this->minLength = $minLength;
        if ($alphabet) $this->alphabet = $alphabet;
    }

    public function encode($id) {
        $id = (int)$id;
        $alphabet = $this->alphabet;
        $len = strlen($alphabet);
        $hash = '';
        
        // Basic obfuscation using salt
        $id = $id + hexdec(substr(md5($this->salt), 0, 4));

        while ($id > 0) {
            $hash = $alphabet[$id % $len] . $hash;
            $id = floor($id / $len);
        }
        
        return str_pad($hash, $this->minLength, '0', STR_PAD_LEFT);
    }

    public function decode($hash) {
        $alphabet = $this->alphabet;
        $len = strlen($alphabet);
        $id = 0;
        
        $hash = ltrim($hash, '0');
        
        for ($i = 0; $i < strlen($hash); $i++) {
            $pos = strpos($alphabet, $hash[$i]);
            if ($pos === false) return [];
            $id = $id * $len + $pos;
        }
        
        $id = $id - hexdec(substr(md5($this->salt), 0, 4));
        return [$id];
    }
}
