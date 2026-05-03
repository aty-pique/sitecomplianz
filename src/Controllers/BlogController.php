<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class BlogController
{
    /** @var array<string, mixed>|null */
    private static ?array $config = null;

    /** @return array<string, mixed> */
    private static function loadConfig(): array
    {
        if (self::$config === null) {
            $file           = ROOT_PATH . '/config/blog.php';
            self::$config   = file_exists($file) ? (array) require $file : [];
        }

        return self::$config;
    }

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

    /**
     * @param array<int, array<string, mixed>> $articles
     * @return array<int, array<string, mixed>>
     */
    private static function annotateArticles(array $articles): array
    {
        foreach ($articles as $i => $row) {
            $articles[$i]['date_label'] = self::frDateLabel((string) ($row['date'] ?? ''));
        }

        return $articles;
    }

    /**
     * @param array<int, array<string, mixed>> $articles
     * @return array<int, int>
     */
    private static function countByPole(array $articles): array
    {
        $counts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0];
        foreach ($articles as $a) {
            $p = (int) ($a['pole'] ?? 0);
            if (isset($counts[$p])) {
                $counts[$p]++;
            }
        }

        return $counts;
    }

    /**
     * @param array<int, array<string, mixed>> $articles
     * @return array<int, array<string, mixed>>
     */
    private static function filterArticles(array $articles, ?string $poleFilter, string $q): array
    {
        $qNorm = mb_strtolower(trim($q));
        $out     = [];
        foreach ($articles as $a) {
            if ($poleFilter !== null && $poleFilter !== '' && $poleFilter !== 'all') {
                if ((string) ($a['pole'] ?? '') !== $poleFilter) {
                    continue;
                }
            }
            if ($qNorm !== '') {
                $hay = mb_strtolower(
                    ($a['title'] ?? '') . ' ' . ($a['excerpt'] ?? '')
                );
                if (!str_contains($hay, $qNorm)) {
                    continue;
                }
            }
            $out[] = $a;
        }

        return $out;
    }

    public function index(Request $request, array $params = []): void
    {
        $cfg      = self::loadConfig();
        $articles = self::annotateArticles((array) ($cfg['articles'] ?? []));
        $poles    = (array) ($cfg['poles'] ?? []);

        $poleRaw = $request->get('pole');
        $pole    = is_string($poleRaw) || is_numeric($poleRaw) ? (string) $poleRaw : null;
        if ($pole === 'all' || $pole === '') {
            $pole = null;
        }

        $q = trim((string) $request->get('q', ''));

        $filtered = self::filterArticles($articles, $pole, $q);
        $counts   = self::countByPole($articles);
        $total    = count($articles);

        $featured = null;
        foreach ($filtered as $a) {
            if (!empty($a['featured'])) {
                $featured = $a;
                break;
            }
        }

        $featuredPole = null;
        if ($featured !== null) {
            $fpid = (int) ($featured['pole'] ?? 0);
            foreach ($poles as $p) {
                if ((int) ($p['id'] ?? -1) === $fpid) {
                    $featuredPole = $p;
                    break;
                }
            }
        }

        $featuredSlug = $featured['slug'] ?? null;
        $gridArticles = [];
        foreach ($filtered as $a) {
            if ($featuredSlug !== null && ($a['slug'] ?? '') === $featuredSlug) {
                continue;
            }
            $gridArticles[] = $a;
        }

        $qParams = $q !== '' ? ['q' => $q] : [];
        $urlAll  = '/blog' . ($qParams ? '?' . http_build_query($qParams) : '');

        $urlByPole = [];
        for ($i = 0; $i <= 4; $i++) {
            $p         = array_merge($qParams, ['pole' => (string) $i]);
            $urlByPole[$i] = '/blog?' . http_build_query($p);
        }

        View::render('pages/blog-index.twig', [
            'title'                => (string) ($cfg['meta_title'] ?? 'Blog'),
            'meta_description'     => (string) ($cfg['meta_description'] ?? ''),
            'blog_poles'           => $poles,
            'articles_all'         => $articles,
            'articles_filtered'    => $filtered,
            'articles_grid'        => $gridArticles,
            'featured_article'     => $featured,
            'featured_pole'        => $featuredPole,
            'pole_filter'          => $pole,
            'search_q'             => $q,
            'counts_by_pole'       => $counts,
            'article_total_count'  => $total,
            'url_blog_all'         => $urlAll,
            'url_blog_pole'        => $urlByPole,
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $slug = isset($params['slug']) ? (string) $params['slug'] : '';
        $cfg  = self::loadConfig();
        $rows = self::annotateArticles((array) ($cfg['articles'] ?? []));

        $article = null;
        foreach ($rows as $row) {
            if (($row['slug'] ?? '') === $slug) {
                $article = $row;
                break;
            }
        }

        if ($article === null) {
            http_response_code(404);
            require ROOT_PATH . '/templates/pages/404.php';
            return;
        }

        $poleId = (int) ($article['pole'] ?? 0);
        $related = [];
        foreach ($rows as $row) {
            if (($row['slug'] ?? '') === $slug) {
                continue;
            }
            if ((int) ($row['pole'] ?? -1) !== $poleId) {
                continue;
            }
            $related[] = $row;
            if (count($related) >= 3) {
                break;
            }
        }

        $poles    = (array) ($cfg['poles'] ?? []);
        $poleInfo = null;
        foreach ($poles as $p) {
            if ((int) ($p['id'] ?? -1) === $poleId) {
                $poleInfo = $p;
                break;
            }
        }
        if ($poleInfo === null) {
            $poleInfo = $poles[0] ?? null;
        }

        View::render('pages/blog-article.twig', [
            'title'            => (string) ($article['title'] ?? 'Article'),
            'meta_description' => (string) ($article['excerpt'] ?? ''),
            'article'          => $article,
            'pole_info'        => $poleInfo,
            'related'          => $related,
        ]);
    }
}
