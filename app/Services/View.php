<?php

declare(strict_types=1);

namespace App\Services;

class View
{
    /**
     * Renders a template with optional data and HTTP response status.
     *
     * @param string $template The name of the template to render (without .php extension).
     * @param int $responseStatus The HTTP response status code (default is 200).
     * @param array<string, mixed> $data An associative array of data to extract into the template.
     */
    public static function render(string $template, int $responseStatus = 200, array $data = []): void
    {
        extract($data);
        http_response_code($responseStatus);
        include APP_ROOT . '/app/Views/layout/header.php';
        include APP_ROOT . '/app/Views/' . $template . '.php';
        include APP_ROOT . '/app/Views/layout/footer.php';
    }

    /**
     * Renders a JSON response with optional data and HTTP response status.
     *
     * @param array<mixed> $data An associative array of data to encode as JSON.
     * @param int $responseStatus The HTTP response status code (default is 200).
     */
    public static function renderJson(array $data, int $responseStatus = 200): void
    {
        http_response_code($responseStatus);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
