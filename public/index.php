<?php

require_once join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);

use Sheetpost\Application;

$app = new Application();
$app->run();