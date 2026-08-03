<?php

declare(strict_types=1);

namespace App\Services;

class View
{
    public static function render(string $template, int $responseStatus = 200, array $data = []): void
    {
        extract($data);
        http_response_code($responseStatus);
        include APP_ROOT . '/app/Views/layout/header.php';
        include APP_ROOT . '/app/Views/' . $template . '.php';
        include APP_ROOT . '/app/Views/layout/footer.php';
    }

    public static function renderJson(array $data, int $responseStatus = 200): void
    {
        http_response_code($responseStatus);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
