<?php

declare(strict_types=1);

use DataTypeExample\GetArticlesParameters;
use VarMap\ArrayVarMap;

require __DIR__ . "/../vendor/autoload.php";

$varmap = new ArrayVarMap(['limit' => 5]);

[$articleGetIndexParams, $errors] = GetArticlesParameters::createOrErrorFromVarMap($varmap);

echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
echo "Ordering: " . var_export($articleGetIndexParams->getOrdering()->toOrderArray(), true) . PHP_EOL;

echo "\nExample behaved as expected.\n";
exit(0);
