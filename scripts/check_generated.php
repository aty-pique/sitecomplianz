<?php
define('ROOT_PATH', dirname(__DIR__));
$b = require dirname(__DIR__) . '/config/seo_briefs.php';
$u = require dirname(__DIR__) . '/config/page_urls.php';
echo 'Briefs : ' . count($b) . PHP_EOL;
echo 'URLs   : ' . count($u) . PHP_EOL;
$keys = array_keys($b);
echo PHP_EOL . 'Premiers slugs :' . PHP_EOL;
foreach (array_slice($keys, 0, 5) as $s) echo '  ' . $s . PHP_EOL;
echo '  ...' . PHP_EOL;
echo PHP_EOL . 'Derniers slugs :' . PHP_EOL;
foreach (array_slice($keys, -5) as $s) echo '  ' . $s . PHP_EOL;
