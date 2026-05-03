<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class WikiController
{
    /** @var array<string, mixed>|null */
    private static ?array $config = null;

    /** @return array<string, mixed> */
    private static function loadConfig(): array
    {
        if (self::$config === null) {
            $file         = ROOT_PATH . '/config/wiki.php';
            self::$config = file_exists($file) ? (array) require $file : [];
        }

        return self::$config;
    }

    /** @return list<array{slug:string,label:string,section:string,tags:string}> */
    private static function flattenPagesForSearch(array $config): array
    {
        $out = [];
        foreach ($config['pages'] ?? [] as $slug => $page) {
            if (!is_string($slug) || !is_array($page)) {
                continue;
            }
            $tags = $page['tags'] ?? [];
            $out[] = [
                'slug'    => $slug,
                'label'   => (string) ($page['h1'] ?? $page['title'] ?? $slug),
                'section' => (string) ($page['description'] ?? ''),
                'tags'    => is_array($tags) ? implode(' ', $tags) : '',
            ];
        }

        return $out;
    }

    public function index(Request $request, array $params = []): void
    {
        $config = self::loadConfig();
        $q      = strtolower(trim((string) $request->get('q', '')));
        $cards  = $config['hub_categories'] ?? [];
        if ($q !== '') {
            $cards = array_values(array_filter($cards, static function ($cat) use ($q): bool {
                if (!is_array($cat)) {
                    return false;
                }
                $hay = strtolower(
                    ($cat['title'] ?? '') . ' ' .
                    ($cat['description'] ?? '') . ' ' .
                    json_encode($cat['links'] ?? [], JSON_UNESCAPED_UNICODE)
                );

                return str_contains($hay, $q);
            }));
        }

        View::render('pages/wiki/index.twig', [
            'title'         => 'Wiki — Documentation',
            'wiki_config'   => $config,
            'wiki_search'   => $q,
            'wiki_cards'    => $cards,
            'wiki_flat'     => self::flattenPagesForSearch($config),
            'wiki_hub_mode' => true,
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $slug   = isset($params['slug']) && is_string($params['slug']) ? $params['slug'] : '';
        $config = self::loadConfig();
        $pages  = $config['pages'] ?? [];
        if ($slug === '' || !isset($pages[$slug]) || !is_array($pages[$slug])) {
            http_response_code(404);
            View::render('pages/wiki/not-found.twig', [
                'title' => 'Page wiki introuvable',
            ]);

            return;
        }

        $page = $pages[$slug];
        View::render('pages/wiki/doc.twig', [
            'title'       => (string) ($page['title'] ?? 'Wiki'),
            'description' => (string) ($page['description'] ?? ''),
            'wiki_config' => $config,
            'wiki_page'   => $page,
            'wiki_slug'   => $slug,
            'wiki_hub_mode' => false,
        ]);
    }
}
