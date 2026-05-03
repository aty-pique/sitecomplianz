<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class HomeController
{
    private static function frDateLabel(string $ymd): string
    {
        $ts = strtotime($ymd);
        if ($ts === false) {
            return $ymd;
        }
        $months = [
            1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
        ];
        $m = (int) date('n', $ts);
        $j = (int) date('j', $ts);
        $y = (int) date('Y', $ts);

        return $j . ' ' . ($months[$m] ?? '') . ' ' . $y;
    }

    public function index(Request $request, array $params = []): void
    {
        $homeFile = ROOT_PATH . '/config/home.php';
        $home     = file_exists($homeFile) ? (array) require $homeFile : [];

        $polesFile     = ROOT_PATH . '/config/poles_landing.php';
        $poleLandings  = file_exists($polesFile) ? (array) require $polesFile : [];
        $poleOrder     = [
            'audit-conformite',
            'solutions-developpement',
            'performance-digitale',
            'support-maintenance',
            'intelligence-artificielle',
        ];
        $homePoles = [];
        foreach ($poleOrder as $key) {
            if (isset($poleLandings[$key])) {
                $row            = $poleLandings[$key];
                $row['hub_key'] = $key;
                $homePoles[]    = $row;
            }
        }

        $packsFile = ROOT_PATH . '/config/packs.php';
        $allPacks  = file_exists($packsFile) ? (array) require $packsFile : [];
        $slugs     = (array) ($home['featured_pack_slugs'] ?? []);
        $homePacks = [];
        foreach ($slugs as $slug) {
            if (isset($allPacks[$slug])) {
                $homePacks[$slug] = $allPacks[$slug];
            }
        }

        $blogFile = ROOT_PATH . '/config/blog.php';
        $blogData = file_exists($blogFile) ? (array) require $blogFile : [];
        $articles = (array) ($blogData['articles'] ?? []);
        usort(
            $articles,
            static fn ($a, $b): int => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? ''))
        );
        $blogTeaser = array_slice($articles, 0, 3);
        foreach ($blogTeaser as $i => $row) {
            $blogTeaser[$i]                   = $row;
            $blogTeaser[$i]['date_label'] = self::frDateLabel((string) ($row['date'] ?? ''));
        }

        View::render('pages/home.twig', [
            'title'            => (string) ($home['meta_title'] ?? 'Accueil'),
            'meta_description' => (string) ($home['meta_description'] ?? ''),
            'home'             => $home,
            'home_poles'       => $homePoles,
            'home_packs'       => $homePacks,
            'home_blog'        => $blogTeaser,
        ]);
    }
}
