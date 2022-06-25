<?php

require_once join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);

use Dotenv\Dotenv;
use Sheetpost\Models\LoggerWrapper;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
Dotenv::createImmutable(dirname(__DIR__))->load();

$logger = new LoggerWrapper("Logger");

$requestedPath = rtrim(explode('?', str_replace('-v2', '', $_SERVER['REQUEST_URI']))[0], '/');
$responseClass = str_replace(['/ ', '- ', 'Api'], ['\\', '', 'API'], ucwords(str_replace(['/', '-'], ['/ ', '- '], $requestedPath)));

if (class_exists($responseClass)) {
    $response = new $responseClass($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    echo json_encode($response->getResponse($_GET));
} else {
    echo json_encode(['success' => false, 'error' => 'unknown method']);
}