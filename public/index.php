<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('BASE_PATH', ROOT_PATH);

require_once ROOT_PATH . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Core\Request;

// Chargement des variables d'environnement
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// Gestion des erreurs selon l'environnement
$appEnv = $_ENV['APP_ENV'] ?? 'production';

if ($appEnv === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Démarrage du routeur
$router = new Router();
$request = new Request();

require_once ROOT_PATH . '/config/routes.php';

$router->dispatch($request);
