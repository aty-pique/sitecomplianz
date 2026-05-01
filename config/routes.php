<?php

use App\Controllers\HomeController;

/**
 * Définition des routes de l'application.
 */

$router->get('/', [HomeController::class, 'index']);
