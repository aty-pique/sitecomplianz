<?php

declare(strict_types=1);

$path = dirname(__DIR__) . '/public/assets/docs/Questionnaire besoins.docx';
if (!is_readable($path)) {
    fwrite(STDERR, "Fichier introuvable: $path\n");
    exit(1);
}

$z = new ZipArchive();
if ($z->open($path) !== true) {
    fwrite(STDERR, "Ouverture ZIP impossible\n");
    exit(1);
}
$xml = $z->getFromName('word/document.xml');
$z->close();
if ($xml === false) {
    fwrite(STDERR, "word/document.xml absent\n");
    exit(1);
}

$d = new DOMDocument();
$d->loadXML($xml);
$xp = new DOMXPath($d);
$xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
$nodes = $xp->query('//w:t');
$parts = [];
foreach ($nodes as $n) {
    $parts[] = $n->textContent;
}
// Word split words across runs; join then normalize breaks
$text = str_replace(["\r\n", "\r"], "\n", implode('', $parts));
$text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
echo $text;
