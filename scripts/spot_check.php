<?php
define('ROOT_PATH', dirname(__DIR__));
$b = require dirname(__DIR__) . '/config/seo_briefs.php';
$p = $b['/conformite-rgpd'];
echo 'Titre    : ' . $p['title'] . PHP_EOL;
echo 'KW       : ' . $p['kw_main']['keyword'] . ' | Vol:' . $p['kw_main']['volume'] . ' KD:' . $p['kw_main']['kd'] . PHP_EOL;
echo 'Cocon    : ' . $p['cocon']['name'] . PHP_EOL;
echo 'Interne  : ' . count($p['maillage_interne']) . ' liens' . PHP_EOL;
echo 'Inter    : ' . count($p['maillage_inter_cocons']) . ' liens' . PHP_EOL;
echo 'Blog     : ' . count($p['liens_blog']) . ' liens' . PHP_EOL;
echo PHP_EOL . 'Extrait maillage interne :' . PHP_EOL;
foreach (array_slice($p['maillage_interne'], 0, 3) as $l) {
    echo '  ' . $l['from'] . ' → ' . $l['to'] . PHP_EOL;
}
