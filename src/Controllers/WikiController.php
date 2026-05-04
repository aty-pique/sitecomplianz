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

    /**
     * @return list<array{slug:string,label:string,description:string,accent:string}>
     */
    private static function resolveHubQuickAccess(array $config): array
    {
        $items = $config['hub_quick_access'] ?? [];
        $pages = $config['pages'] ?? [];
        $out   = [];
        foreach ($items as $row) {
            if (!is_array($row) || !isset($row['slug'], $pages[$row['slug']]) || !is_array($pages[$row['slug']])) {
                continue;
            }
            $p = $pages[$row['slug']];
            $out[] = [
                'slug'        => (string) $row['slug'],
                'label'       => (string) ($p['h1'] ?? $p['title'] ?? $row['slug']),
                'description' => (string) ($p['description'] ?? ''),
                'accent'      => (string) ($row['accent'] ?? 'blue'),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{slug:string,label:string,tag:string,tag_class:string}>
     */
    private static function resolveHubPopular(array $config): array
    {
        $slugs = $config['hub_popular_slugs'] ?? [];
        $pages = $config['pages'] ?? [];
        $out   = [];
        foreach ($slugs as $slug) {
            if (!is_string($slug) || !isset($pages[$slug]) || !is_array($pages[$slug])) {
                continue;
            }
            $p       = $pages[$slug];
            $section = (string) ($p['nav_section'] ?? '');
            $tag     = match ($section) {
                'systeme'     => 'Système',
                'integration' => 'API',
                'support'     => 'Support',
                default       => 'Métho',
            };
            $tagClass = match ($section) {
                'systeme'     => 'wiki-hub-tagpill--blue',
                'integration' => 'wiki-hub-tagpill--amber',
                'support'     => 'wiki-hub-tagpill--slate',
                default       => 'wiki-hub-tagpill--green',
            };
            $out[] = [
                'slug'      => $slug,
                'label'     => (string) ($p['h1'] ?? $p['title'] ?? $slug),
                'tag'       => $tag,
                'tag_class' => $tagClass,
            ];
        }

        return $out;
    }

    /**
     * @return list<array{slug:string,label:string,updated:string}>
     */
    private static function resolveHubRecent(array $config, int $limit = 4): array
    {
        $pages = $config['pages'] ?? [];
        $rows  = [];
        foreach ($pages as $slug => $p) {
            if (!is_string($slug) || !is_array($p)) {
                continue;
            }
            $rows[] = [
                'slug'    => $slug,
                'label'   => (string) ($p['h1'] ?? $p['title'] ?? $slug),
                'updated' => (string) ($p['updated'] ?? ''),
            ];
        }
        usort($rows, static function (array $a, array $b): int {
            return strcmp($b['updated'], $a['updated']);
        });

        return array_slice($rows, 0, $limit);
    }

    /**
     * @return list<array{slug:string,label:string,text:string,n:int}>
     */
    private static function resolveHubGettingStarted(array $config): array
    {
        $steps = $config['hub_getting_started'] ?? [];
        $pages = $config['pages'] ?? [];
        $out   = [];
        $n     = 0;
        foreach ($steps as $step) {
            if (!is_array($step) || !isset($step['slug'], $pages[$step['slug']]) || !is_array($pages[$step['slug']])) {
                continue;
            }
            ++$n;
            $p = $pages[$step['slug']];
            $out[] = [
                'n'     => $n,
                'slug'  => (string) $step['slug'],
                'label' => (string) ($p['h1'] ?? $p['title'] ?? $step['slug']),
                'text'  => (string) ($step['text'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{slug:string,title:string,note:string,date:string,href:string}>
     */
    private static function resolveHubChangelog(array $config): array
    {
        $raw   = $config['hub_changelog'] ?? [];
        $pages = $config['pages'] ?? [];
        $out   = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $slug = (string) ($row['slug'] ?? '');
            $title = $slug !== '' && isset($pages[$slug]) && is_array($pages[$slug])
                ? (string) ($pages[$slug]['h1'] ?? $pages[$slug]['title'] ?? $slug)
                : $slug;
            $out[] = [
                'slug'  => $slug,
                'title' => $title,
                'note'  => (string) ($row['note'] ?? ''),
                'date'  => (string) ($row['date'] ?? ''),
                'href'  => $slug !== '' ? '/wiki/' . $slug : '/wiki',
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
            'title'               => 'Wiki — Documentation',
            'wiki_config'         => $config,
            'wiki_search'         => $q,
            'wiki_cards'          => $cards,
            'wiki_flat'           => self::flattenPagesForSearch($config),
            'wiki_hub_mode'       => true,
            'wiki_hub_quick'      => self::resolveHubQuickAccess($config),
            'wiki_hub_popular'    => self::resolveHubPopular($config),
            'wiki_hub_recent'     => self::resolveHubRecent($config),
            'wiki_hub_started'    => self::resolveHubGettingStarted($config),
            'wiki_hub_useful'     => is_array($config['hub_useful_links'] ?? null) ? $config['hub_useful_links'] : [],
            'wiki_hub_changelog'  => self::resolveHubChangelog($config),
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
