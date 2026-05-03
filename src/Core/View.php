<?php

declare(strict_types=1);

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

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
            $appUrl = (string) ($_ENV['APP_URL'] ?? 'http://localhost:8000');
            self::$twig->addGlobal('app_url', $appUrl);
            self::$twig->addGlobal('asset_base', rtrim($appUrl, '/'));

            $dfb = strtolower(trim((string) ($_ENV['DEV_CLIENT_FEEDBACK_ENABLED'] ?? '')));
            self::$twig->addGlobal(
                'dev_client_feedback_enabled',
                in_array($dfb, ['1', 'true', 'yes', 'on'], true)
            );

            $pageUrlsFile = ROOT_PATH . '/config/page_urls.php';
            self::$twig->addGlobal(
                'page_urls',
                file_exists($pageUrlsFile) ? (array) require $pageUrlsFile : []
            );

            self::$twig->addFunction(new TwigFunction(
                'inline_svg',
                static function (string $path): string {
                    $fullPath = ROOT_PATH . '/public' . $path;
                    return file_exists($fullPath) ? (string) file_get_contents($fullPath) : '';
                },
                ['is_safe' => ['html']]
            ));
        }

        return self::$twig;
    }

    public static function render(string $template, array $data = []): void
    {
        echo self::getTwig()->render($template, $data);
    }
}
