<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

use App\Services\QuotePricingEstimator;

$q = [
    'poles'        => ['p1'],
    'p1'           => ['project_types' => ['Site / refonte']],
    'company_size' => '2-10',
];

$e = new QuotePricingEstimator();
print_r($e->estimate($q));
