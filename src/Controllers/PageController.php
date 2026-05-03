<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class PageController
{
    /** @var array<string, mixed> */
    private static array $briefs = [];

    private static function loadBriefs(): void
    {
        if (empty(self::$briefs)) {
            $file = ROOT_PATH . '/config/seo_briefs.php';
            self::$briefs = file_exists($file) ? (array) require $file : [];
        }
    }

    public function show(Request $request, array $params = []): void
    {
        self::loadBriefs();

        $slug  = $request->getUri();
        $brief = self::$briefs[$slug] ?? null;

        /* Page laboratoire / innovation (/hub) */
        if (trim($slug, '/') === 'hub') {
            $hubFile = ROOT_PATH . '/config/hub.php';
            $hubData = file_exists($hubFile) ? (array) require $hubFile : [];
            View::render('pages/hub.twig', [
                'title'            => (string) ($hubData['meta_title'] ?? 'Hub d’innovation'),
                'meta_description' => (string) ($hubData['meta_description'] ?? ''),
                'hub'              => $hubData,
                'slug'             => $slug,
                'brief'            => $brief,
            ]);

            return;
        }

        /* Pages « hub » par pôle (/audit-conformite, /solutions-developpement, …) */
        $poleLandingsFile = ROOT_PATH . '/config/poles_landing.php';
        if (file_exists($poleLandingsFile)) {
            /** @var array<string, array<string, mixed>> $poleLandings */
            $poleLandings = (array) require $poleLandingsFile;
            $poleKey      = trim($slug, '/');
            if ($poleKey !== '' && isset($poleLandings[$poleKey])) {
                $poleData = $poleLandings[$poleKey];
                $packsFile = ROOT_PATH . '/config/packs.php';
                /** @var array<string, array<string, mixed>> $allPacks */
                $allPacks = file_exists($packsFile) ? (array) require $packsFile : [];
                $polePacks = [];
                $poleId    = (int) ($poleData['pole'] ?? -1);
                foreach ($allPacks as $packSlug => $packRow) {
                    if ((int) ($packRow['pole'] ?? -2) === $poleId) {
                        $polePacks[$packSlug] = $packRow;
                    }
                }
                View::render('pages/pole-landing.twig', [
                    'title'      => (string) ($poleData['meta_title'] ?? $poleData['name'] ?? 'Page'),
                    'pole'       => $poleData,
                    'pole_packs' => $polePacks,
                    'slug'       => $slug,
                    'brief'      => $brief,
                ]);

                return;
            }
        }

        // Cherche un template dédié : pages/{segment}.twig puis pages/services/{segment}.twig
        $lastSegment     = ltrim(basename($slug), '/');
        $dedicatedTpl    = 'pages/services/' . $lastSegment . '.twig';
        $dedicatedExists = false;
        foreach (['pages/' . $lastSegment . '.twig', 'pages/services/' . $lastSegment . '.twig'] as $candidate) {
            if (file_exists(ROOT_PATH . '/templates/' . $candidate)) {
                $dedicatedTpl    = $candidate;
                $dedicatedExists = true;
                break;
            }
        }

        $vars = [
            'title' => ($brief['title'] ?? ($params['title'] ?? 'Page')),
            'brief' => $brief,
            'slug'  => $slug,
        ];

        if ($dedicatedExists) {
            View::render($dedicatedTpl, $vars);
        } elseif ($brief !== null) {
            View::render('pages/page-brief.twig', $vars);
        } else {
            View::render('pages/blank.twig', ['title' => $params['title'] ?? 'Page']);
        }
    }
}
