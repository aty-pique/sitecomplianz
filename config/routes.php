<?php

use App\Controllers\BlogController;
use App\Controllers\ContactController;
use App\Controllers\WikiController;
use App\Controllers\DevFeedbackController;
use App\Controllers\HomeController;
use App\Controllers\LabProjectController;
use App\Controllers\PackController;
use App\Controllers\PageController;

/**
 * Routes de l'application.
 * Les pages services sont chargées depuis routes_csv.php (généré automatiquement).
 */

$router->get('/', [HomeController::class, 'index']);

$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact/quote', [ContactController::class, 'quote']);
$router->post('/contact/message', [ContactController::class, 'message']);

$router->get('/nos-packs', [PackController::class, 'index']);
$router->get('/nos-packs/:slug', [PackController::class, 'show']);

$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/:slug', [BlogController::class, 'show']);

$router->get('/wiki', [WikiController::class, 'index']);
$router->get('/wiki/:slug', [WikiController::class, 'show']);

$router->get('/lab/:slug', [LabProjectController::class, 'show']);

/* Retours client — dossier dev-notes/ (désactivé si DEV_CLIENT_FEEDBACK_ENABLED≠1 dans .env) */
if (DevFeedbackController::isEnabled()) {
    $router->get('/dev-feedback', [DevFeedbackController::class, 'index']);
    $router->post('/dev-feedback', [DevFeedbackController::class, 'store']);
    $router->post('/dev-feedback/clear', [DevFeedbackController::class, 'clear']);
}

/* Pages fixes */
$staticPages = [
    '/audit-conformite/audit-conseil/conseil-strategique'         => 'Conseil stratégique',
    '/performance-digitale/structuration-marketing'                       => 'Structuration marketing',
    '/support-maintenance/maintenance/maintenance-evolutive'                      => 'Maintenance évolutive',
    '/intelligence-artificielle/formation-ia/formation-agents-ia'                  => 'Formation composition d\'agents IA',
    '/hub'         => 'Hub',
    '/connexion'   => 'Se connecter',
    '/telecharger' => 'Télécharger l\'app',
    '/a-propos'    => 'A propos',
    /* Landing pages des 5 pôles */
    '/audit-conformite'         => 'Conseil, Audit & Conformité',
    '/solutions-developpement'  => 'Solutions & Développement',
    '/performance-digitale'     => 'Performance Digitale & Stratégie',
    '/support-maintenance'      => 'Support & Maintenance',
    '/intelligence-artificielle' => 'Intelligence Artificielle',
];

foreach ($staticPages as $path => $title) {
    $router->get($path, static function ($request, $params) use ($title): void {
        (new PageController())->show($request, array_merge($params, ['title' => $title]));
    });
}

/* Pages services (générées depuis le CSV) */
require __DIR__ . '/routes_csv.php';
