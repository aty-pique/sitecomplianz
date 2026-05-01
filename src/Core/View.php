<?php

declare(strict_types=1);

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private static ?Environment $twig = null;

    private static function getTwig(): Environment
    {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(ROOT_PATH . '/templates');
            self::$twig = new Environment($loader, [
                'cache'       => false,
                'debug'       => ($_ENV['APP_ENV'] ?? 'production') === 'development',
                'auto_reload' => true,
            ]);

            self::$twig->addGlobal('app_name', $_ENV['APP_NAME'] ?? 'Complianz');
            self::$twig->addGlobal('app_url', $_ENV['APP_URL'] ?? 'http://localhost:8000');
        }

        return self::$twig;
    }

    public static function render(string $template, array $data = []): void
    {
        echo self::getTwig()->render($template, $data);
    }
}
