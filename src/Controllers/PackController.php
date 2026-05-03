<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

/**
 * Pages détail des offres pack (Nos packs du méga menu).
 */
class PackController
{
    public function index(Request $request, array $params): void
    {
        $file = ROOT_PATH . '/config/packs.php';
        /** @var array<string, array<string, mixed>> $packs */
        $packs = file_exists($file) ? (array) require $file : [];

        $poles = [
            ['id' => 0, 'name' => 'Conseil, Audit & Conformité', 'url' => '/audit-conformite'],
            ['id' => 1, 'name' => 'Solutions & Développement', 'url' => '/solutions-developpement'],
            ['id' => 2, 'name' => 'Performance Digitale & Stratégie', 'url' => '/performance-digitale'],
            ['id' => 3, 'name' => 'Support & Maintenance', 'url' => '/support-maintenance'],
            ['id' => 4, 'name' => 'Intelligence Artificielle', 'url' => '/intelligence-artificielle'],
        ];

        View::render('pages/packs-index.twig', [
            'title' => 'Nos packs par pôle',
            'packs' => $packs,
            'poles' => $poles,
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $slug = $params['slug'] ?? '';
        $file = ROOT_PATH . '/config/packs.php';
        /** @var array<string, array<string, mixed>> $packs */
        $packs = file_exists($file) ? (array) require $file : [];

        if ($slug === '' || !isset($packs[$slug])) {
            http_response_code(404);
            require ROOT_PATH . '/templates/pages/404.php';
            return;
        }

        $pack = self::enrichPack($packs[$slug]);
        View::render('pages/pack-detail.twig', [
            'title'       => $pack['title'] ?? 'Pack',
            'pack'        => $pack,
            'slug'        => $slug,
            'other_packs' => self::otherPacks($packs, $slug, $pack),
        ]);
    }

    /**
     * Complète les clés optionnelles (bénéfices, blocs contenu, etc.) pour l’affichage landing.
     *
     * @param array<string, mixed> $pack
     * @return array<string, mixed>
     */
    private static function enrichPack(array $pack): array
    {
        if (($pack['objective'] ?? '') === '') {
            $pack['objective'] = (string) ($pack['tagline'] ?? '');
        }
        if (($pack['value_headline'] ?? '') === '') {
            $pack['value_headline'] = (string) ($pack['intro'] ?? '');
        }
        if (empty($pack['benefits'])) {
            $pack['benefits'] = $pack['deliverables'] ?? [];
        }
        if (empty($pack['characteristics'])) {
            $ch = [];
            if (($pack['duration'] ?? '') !== '') {
                $ch[] = 'Durée indicative : ' . $pack['duration'];
            }
            $ch[] = 'Ateliers et points d\'étape formalisés avec votre équipe.';
            $ch[] = 'Livrables exploitables par la direction et les opérationnels.';
            $pack['characteristics'] = $ch;
        }
        if (($pack['client_summary'] ?? '') === '') {
            $pack['client_summary'] = (string) ($pack['tagline'] ?? '');
        }
        if (empty($pack['content_blocks']) && !empty($pack['related'])) {
            $pack['content_blocks'] = [];
            foreach ($pack['related'] as $r) {
                $pack['content_blocks'][] = [
                    'title'      => $r['label'],
                    'bullets'    => [
                        'Prestation alignée sur les objectifs du pack et votre contexte métier.',
                        'Coordination avec les autres volets du parcours Complianz.',
                    ],
                    'url'        => $r['url'],
                    'link_label' => 'Voir la fiche service',
                ];
            }
        }
        if (empty($pack['hero_checklist'])) {
            $lines = array_merge(
                $pack['deliverables'] ?? [],
                array_map(static fn (array $r): string => $r['label'], $pack['related'] ?? [])
            );
            $pack['hero_checklist'] = array_values(array_unique(array_slice($lines, 0, 6)));
        }

        return $pack;
    }

    /**
     * @param array<string, array<string, mixed>> $packs
     * @return list<array{slug: string, title: string, price: string, tagline: string, signature: bool, pole: int, pole_name: string}>
     */
    private static function otherPacks(array $packs, string $currentSlug, array $currentPack): array
    {
        $currentPole = (int) ($currentPack['pole'] ?? 0);
        $candidates  = [];
        foreach ($packs as $s => $p) {
            if ($s === $currentSlug) {
                continue;
            }
            $candidates[] = [
                'slug'  => $s,
                'pole'  => (int) ($p['pole'] ?? 0),
                'title' => (string) ($p['title'] ?? ''),
                'pack'  => $p,
            ];
        }
        usort($candidates, static function (array $a, array $b) use ($currentPole): int {
            $ap = $a['pole'] === $currentPole ? 0 : 1;
            $bp = $b['pole'] === $currentPole ? 0 : 1;
            if ($ap !== $bp) {
                return $ap <=> $bp;
            }

            return strcmp($a['title'], $b['title']);
        });

        $out = [];
        foreach (array_slice($candidates, 0, 4) as $c) {
            $p       = $c['pack'];
            $out[] = [
                'slug'       => $c['slug'],
                'title'      => (string) ($p['title'] ?? ''),
                'price'      => (string) ($p['price'] ?? ''),
                'tagline'    => (string) ($p['tagline'] ?? ''),
                'signature'  => (bool) ($p['signature'] ?? false),
                'pole'       => (int) ($p['pole'] ?? 0),
                'pole_name'  => (string) ($p['pole_name'] ?? ''),
            ];
        }

        return $out;
    }
}
