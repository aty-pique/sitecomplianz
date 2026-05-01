<?php

/**
 * Routeur pour le serveur PHP intégré.
 * Sert les fichiers statiques directement, redirige tout vers index.php sinon.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Servir les fichiers statiques existants directement
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Tout rediriger vers index.php
require_once __DIR__ . '/index.php';
