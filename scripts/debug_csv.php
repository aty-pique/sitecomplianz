<?php
$h = fopen('C:/Users/elisa/Downloads/complianz_pages_service.csv', 'r');
$headers = fgetcsv($h);
fclose($h);
echo "Nombre de colonnes : " . count($headers) . "\n";
foreach ($headers as $i => $h) {
    echo "  [$i] => " . bin2hex(substr($h, 0, 10)) . " = $h\n";
}
