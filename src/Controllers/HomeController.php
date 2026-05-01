<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class HomeController
{
    public function index(Request $request, array $params = []): void
    {
        View::render('pages/home.twig', [
            'title'    => 'Accueil',
            'subtitle' => 'La solution de conformité RGPD pour votre site web',
        ]);
    }
}
