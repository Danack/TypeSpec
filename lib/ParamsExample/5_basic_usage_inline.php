<?php

declare(strict_types=1);

use ParamsExample\GetArticlesParams;
use VarMap\ArrayVarMap;

require __DIR__ . "/../../vendor/autoload.php";

$varMap = new ArrayVarMap([]);

$articleGetIndexParams = GetArticlesParams::fromVarMapAsObject($varMap);

echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
echo "Ordering: " . var_export($articleGetIndexParams->getOrdering()->toOrderArray(), true) . PHP_EOL;

echo "\nExample behaved as expected.\n";
exit(0);
