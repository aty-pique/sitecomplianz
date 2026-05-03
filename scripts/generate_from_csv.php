<?php

/**
 * Générateur automatique depuis complianz_pages_service.csv
 * Structure URL : /[pole-slug]/[pilier-slug]/[soutien-slug]
 *
 * Lance : php scripts/generate_from_csv.php
 * Produit : config/seo_briefs.php + config/page_urls.php + config/routes_csv.php
 */

define('ROOT', dirname(__DIR__));

// CSV service  : source des routes et URLs (112 pages de service)
$csvService = 'C:/Users/elisa/Downloads/complianz_pages_service.csv';
// CSV SEO complet : source enrichie pour les mots-clés (646 lignes, inclut blog/ressources)
$csvSeo     = 'C:/Users/elisa/Downloads/complianz_pages_seo.csv';

$csvPath = $csvService;   // alias pour la suite du script (routing)

if (!file_exists($csvService)) {
    die("Fichier CSV service introuvable : $csvService\n");
}
if (!file_exists($csvSeo)) {
    echo "⚠ CSV SEO introuvable, mots-clés limités à 1 par page.\n";
    $csvSeo = null;
}

/* ── Mapping pôle → slug URL ─────────────────────────────────────── */
$poleSlugs = [
    'P1' => 'audit-conformite',
    'P2' => 'solutions-developpement',
    'P3' => 'performance-digitale',
    'P4' => 'support-maintenance',
    'P5' => 'intelligence-artificielle',
];

/* ── 1. Lecture du CSV ───────────────────────────────────────────── */
$handle  = fopen($csvPath, 'r');
$headers = fgetcsv($handle);
// Supprime le BOM UTF-8 éventuel sur le premier header
$headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
// Supprime aussi les guillemets résiduels (certains exports CSV les incluent)
$headers = array_map(fn($h) => trim($h, '"'), $headers);
$cols    = array_flip($headers);

$rows = [];
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) >= count($headers)) {
        $rows[] = $row;
    }
}
fclose($handle);
echo "CSV lu : " . count($rows) . " lignes.\n";

/* ── 2. Nettoyage de slug ────────────────────────────────────────── */
function cleanSlug(string $slug): string
{
    // Tronque les slugs à triple tiret : "ia-agentique---def..." → "ia-agentique"
    $slug = preg_replace('/---.*$/', '', $slug);
    // Supprime les tirets multiples résiduels
    return trim(preg_replace('/-{2,}/', '-', $slug), '-');
}

/* ── 3. Premier passage : indexer les piliers P5 par cocon_id ───── */
$p5PilierSlugByCocon = [];   // cocon_id → pilier clean slug
foreach ($rows as $row) {
    if ($row[$cols['pole_id']] === 'P5' && $row[$cols['type']] === 'pilier') {
        $coconId = $row[$cols['cocon_id']];
        $p5PilierSlugByCocon[$coconId] = cleanSlug($row[$cols['slug']]);
    }
}

/* ── 4. Indexer les cocones (structure pilier/soutiens) ─────────── */
$coconIndex = [];
foreach ($rows as $row) {
    $type     = $row[$cols['type']];
    $coconId  = $row[$cols['cocon_id']];
    $coconNom = $row[$cols['cocon_nom']];
    $titre    = $row[$cols['titre']];

    if (!isset($coconIndex[$coconId])) {
        $coconIndex[$coconId] = [
            'name'     => $coconNom,
            'piliers'  => [],
            'soutiens' => [],
        ];
    }
    match ($type) {
        'pilier'  => $coconIndex[$coconId]['piliers'][]  = $titre,
        'soutien' => $coconIndex[$coconId]['soutiens'][] = $titre,
        default   => null,
    };
}

/* ── 5. Parseur de maillage ──────────────────────────────────────── */
function parseMaillage(string $str): array
{
    if (trim($str) === '') return [];
    $result = [];
    foreach (explode(' | ', $str) as $item) {
        $item = trim($item);
        if (preg_match('/^(.+?)\s+\[(\w+)\]$/', $item, $m)) {
            $result[] = ['title' => trim($m[1]), 'type' => $m[2]];
        }
    }
    return $result;
}

/* ── 6. Calcul URL interne pour une ligne ────────────────────────── */
function buildUrl(array $row, array $cols, array $poleSlugs, array $p5PilierSlugByCocon): ?string
{
    $poleId = $row[$cols['pole_id']];
    $type   = $row[$cols['type']];

    if (!in_array($type, ['pilier', 'soutien'], true)) return null;
    if (!isset($poleSlugs[$poleId])) return null;

    $polePrefix = '/' . $poleSlugs[$poleId];

    if ($poleId !== 'P5') {
        /* P1–P4 : on préfixe l'URL CSV existante */
        $csvUrl = $row[$cols['url']];
        // ignorer les URLs absolues (sécurité)
        if (str_starts_with($csvUrl, 'http')) return null;
        return $polePrefix . $csvUrl;
    }

    /* P5 : construction depuis slug + cocon */
    $ownSlug = cleanSlug($row[$cols['slug']]);
    if ($type === 'pilier') {
        return $polePrefix . '/' . $ownSlug;
    }
    // soutien
    $coconId = $row[$cols['cocon_id']];
    $parentSlug = $p5PilierSlugByCocon[$coconId] ?? null;
    if ($parentSlug === null) return null;
    return $polePrefix . '/' . $parentSlug . '/' . $ownSlug;
}

/* ── 7a. Agrégation des mots-clés secondaires depuis le CSV SEO ──── */
// Collecte TOUS les mots-clés secondaires non vides, par cocon_id,
// depuis le grand CSV (646 lignes, inclut blog/ressources).
$coconKwIndex = [];  // cocon_id → [ keyword → ['vol' => int, 'kd' => int] ]

if ($csvSeo !== null) {
    $hSeo = fopen($csvSeo, 'r');
    $hdrSeo = fgetcsv($hSeo);
    $hdrSeo[0] = preg_replace('/^\xEF\xBB\xBF/', '', $hdrSeo[0]);
    $hdrSeo = array_map(fn($h) => trim($h, '"'), $hdrSeo);
    $colsSeo = array_flip($hdrSeo);

    while (($rSeo = fgetcsv($hSeo)) !== false) {
        if (count($rSeo) < count($hdrSeo)) continue;
        $cid = $rSeo[$colsSeo['cocon_id']];
        $kw  = trim($rSeo[$colsSeo['mot_cle_secondaire']]);
        if ($kw === '') continue;
        $v = (int)($rSeo[$colsSeo['vol_secondaire']] ?? 0);
        $k = (int)($rSeo[$colsSeo['kd_secondaire']]  ?? 0);
        if (!isset($coconKwIndex[$cid][$kw])) {
            $coconKwIndex[$cid][$kw] = ['vol' => $v, 'kd' => $k];
        }
    }
    fclose($hSeo);
    $totalCoconKws = array_sum(array_map('count', $coconKwIndex));
    echo "Index cocon : " . count($coconKwIndex) . " cocons, $totalCoconKws mots-clés secondaires.\n";
}

/* ── 7b. Construction des briefs ─────────────────────────────────── */
$briefs   = [];
$pageUrls = [];

foreach ($rows as $row) {
    $url = buildUrl($row, $cols, $poleSlugs, $p5PilierSlugByCocon);
    if ($url === null) continue;

    $poleId  = $row[$cols['pole_id']];
    $poleNom = $row[$cols['pole_nom']];
    $titre   = $row[$cols['titre']];
    $coconId = $row[$cols['cocon_id']];

    $kwMain  = $row[$cols['mot_cle_principal']];
    $volMain = (int) ($row[$cols['vol_principal']] ?? 0);
    $kdMain  = (int) ($row[$cols['kd_principal']] ?? 0);

    $ratioMain = $kdMain > 0 ? round($volMain / $kdMain, 1) : (float) $volMain;

    // ── Mots-clés secondaires agrégés depuis le cocon entier ──────
    $kwSecondary = [];
    $seenKws     = [];

    // 1. Propre kw secondaire du CSV service (en premier)
    $kwSec   = $row[$cols['mot_cle_secondaire']] ?? '';
    $volSec  = (int) ($row[$cols['vol_secondaire']] ?? 0);
    $kdSec   = (int) ($row[$cols['kd_secondaire']] ?? 0);
    if (!empty($kwSec)) {
        $ratioSec = $kdSec > 0 ? round($volSec / $kdSec, 1) : (float) $volSec;
        $kwSecondary[] = ['keyword' => $kwSec, 'volume' => $volSec, 'kd' => $kdSec, 'ratio' => $ratioSec];
        $seenKws[$kwSec] = true;
    }

    // 2. Tous les kw du cocon issus du CSV SEO (deduplication)
    foreach ($coconKwIndex[$coconId] ?? [] as $kw => $data) {
        if (isset($seenKws[$kw])) continue;
        $ratio = $data['kd'] > 0 ? round($data['vol'] / $data['kd'], 1) : (float) $data['vol'];
        $kwSecondary[] = [
            'keyword' => $kw,
            'volume'  => $data['vol'],
            'kd'      => $data['kd'],
            'ratio'   => $ratio,
        ];
        $seenKws[$kw] = true;
    }

    // Tri par ratio décroissant
    usort($kwSecondary, fn($a, $b) => $b['ratio'] <=> $a['ratio']);

    $maillageOut = parseMaillage($row[$cols['maillage_sortant']] ?? '');
    $maillageIn  = parseMaillage($row[$cols['maillage_entrant']] ?? '');

    $maillageInterne = $maillageInterCocons = $liensBlog = [];
    foreach ($maillageOut as $link) {
        $entry = ['from' => $titre, 'to' => $link['title']];
        match ($link['type']) {
            'interne'       => $maillageInterne[]     = $entry,
            'inter'         => $maillageInterCocons[] = $entry,
            'maillage_blog' => $liensBlog[]           = $entry,
            default         => null,
        };
    }
    foreach ($maillageIn as $link) {
        $entry = ['from' => $link['title'], 'to' => $titre];
        match ($link['type']) {
            'interne'       => $maillageInterne[]     = $entry,
            'inter'         => $maillageInterCocons[] = $entry,
            'maillage_blog' => $liensBlog[]           = $entry,
            default         => null,
        };
    }

    $cocon = $coconIndex[$coconId] ?? ['name' => '', 'piliers' => [], 'soutiens' => []];

    $briefs[$url] = [
        'title'                 => $titre,
        'slug'                  => $url,
        'pole'                  => $poleNom,
        'kw_main'               => ['keyword' => $kwMain, 'volume' => $volMain, 'kd' => $kdMain, 'ratio' => $ratioMain],
        'kw_secondary'          => $kwSecondary,
        'cocon'                 => $cocon,
        'maillage_interne'      => $maillageInterne,
        'maillage_inter_cocons' => $maillageInterCocons,
        'liens_blog'            => $liensBlog,
    ];

    $pageUrls[$titre] = $url;
}

echo "Pages extraites : " . count($briefs) . "\n";

/* ── 8. Encodage PHP ─────────────────────────────────────────────── */
function phpExport(mixed $val, int $depth = 0): string
{
    $pad = str_repeat('    ', $depth);
    if (is_array($val)) {
        if (empty($val)) return '[]';
        $isAssoc = array_keys($val) !== range(0, count($val) - 1);
        $out = "[\n";
        foreach ($val as $k => $v) {
            $out .= $pad . '    ';
            if ($isAssoc) $out .= "'" . addslashes((string) $k) . "' => ";
            $out .= phpExport($v, $depth + 1) . ",\n";
        }
        return $out . $pad . ']';
    }
    if (is_string($val)) return "'" . addslashes($val) . "'";
    if (is_float($val))  return rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.');
    return (string) $val;
}

/* ── 9. seo_briefs.php ───────────────────────────────────────────── */
$out  = "<?php\n\n/**\n * Briefs SEO — généré depuis complianz_pages_service.csv\n * NE PAS ÉDITER manuellement — relancer scripts/generate_from_csv.php\n */\nreturn [\n\n";
foreach ($briefs as $slug => $brief) {
    $out .= "    '" . addslashes($slug) . "' => " . phpExport($brief, 1) . ",\n\n";
}
$out .= "];\n";
file_put_contents(ROOT . '/config/seo_briefs.php', $out);
echo "✔ config/seo_briefs.php (" . count($briefs) . " entrées)\n";

/* ── 10. page_urls.php ───────────────────────────────────────────── */
$out2 = "<?php\n\n/**\n * Table titre → URL — généré depuis complianz_pages_service.csv\n */\nreturn [\n\n";
foreach ($pageUrls as $titre => $url) {
    $out2 .= "    '" . addslashes($titre) . "' => '" . addslashes($url) . "',\n";
}
$out2 .= "\n];\n";
file_put_contents(ROOT . '/config/page_urls.php', $out2);
echo "✔ config/page_urls.php (" . count($pageUrls) . " entrées)\n";

/* ── 11. routes_csv.php ──────────────────────────────────────────── */
$out3  = "<?php\n\n/**\n * Routes générées depuis complianz_pages_service.csv\n * Inclure via : require __DIR__ . '/routes_csv.php';\n */\n\nuse App\\Controllers\\PageController;\n\n\$pages = [\n";
foreach ($briefs as $slug => $brief) {
    $out3 .= "    '" . addslashes($slug) . "' => '" . addslashes($brief['title']) . "',\n";
}
$out3 .= "];\n\nforeach (\$pages as \$path => \$title) {\n    \$router->get(\$path, static function (\$req, \$p) use (\$title): void {\n        (new PageController())->show(\$req, array_merge(\$p, ['title' => \$title]));\n    });\n}\n";
file_put_contents(ROOT . '/config/routes_csv.php', $out3);
echo "✔ config/routes_csv.php (" . count($briefs) . " routes)\n";

/* ── 12. Afficher un résumé des URLs par pôle ───────────────────── */
echo "\n── Résumé des URLs par pôle ──────────────────────\n";
$byPole = [];
foreach ($briefs as $url => $brief) {
    $byPole[$brief['pole']][] = $url;
}
foreach ($byPole as $pole => $urls) {
    echo "  $pole (" . count($urls) . " pages)\n";
    foreach ($urls as $u) echo "    $u\n";
}
echo "\nTerminé !\n";
