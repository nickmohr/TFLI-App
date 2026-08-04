<?php

declare(strict_types=1);

namespace App\Services;

class Router
{
    /**
     * @var array<int, array{requestMethod: string, path: string, class: string, methodName: string}>
     */
    private array $routes = [];

    public function add(string $path, string $requestMethod, string $class, string $methodName): void
    {
        $this->routes[] = [
            'requestMethod' => strtoupper($requestMethod),
            'path' => $path,
            'class' => $class,
            'methodName' => $methodName,
        ];
    }

    public function dispatch(): void
    {

        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        $path = $this->requestUri();
        foreach ($this->routes as $route) {
            if ($route['requestMethod'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $path);
            if ($params !== null) {
                (new $route['class']())->{$route['methodName']}(...$params);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    /**
     * Absolute base URL of the app (no trailing slash), e.g. http://localhost.
     */
    public static function baseUrl(): string
    {
        // If APP_ENV is set to 'production' and APP_URL is defined, use it as the base URL.
        if (defined('APP_ENV') && APP_ENV === 'production' && defined('APP_URL')) {
            return APP_URL;
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        return $scheme . '://' . $host;
    }

    /**
     * Request path relative to the app's base URL, so the app
     * works at the domain root or in a subdirectory.
     */
    private function requestUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (!is_string($uri) || $uri === '') {
            throw new \Exception('Bad Request', 400);
        }
        return $uri;
    }

    /**
     * Returns captured {placeholder} values, or null if no match.
     * @return array<int, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        if ($pattern === $path) {
            return [];
        }

        // Placeholders match a single path segment.
        $regex = preg_replace('/\\\{[a-zA-Z_]+\\\}/', '([^/]+)', preg_quote($pattern, '#'));
        if (preg_match('#^' . $regex . '$#', $path, $matches) === 1) {
            return array_slice($matches, 1);
        }

        return null;
    }
}
