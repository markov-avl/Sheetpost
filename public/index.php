<?php

require_once join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);
define("TEMPLATES_PATH", dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates');

use Dotenv\Dotenv;
use Sheetpost\Database;
use Sheetpost\LoggerWrapper;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

Dotenv::createImmutable(dirname(__DIR__))->load();

$loader = new FilesystemLoader(TEMPLATES_PATH);
$twig = new Environment($loader);
$logger = new LoggerWrapper("Logger");
$db = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$db->connect();

$authorized =
    isset($_POST['username']) &&
    isset($_POST['password']) &&
    $db->isUserExists($_POST['username'], $_POST['password']);

try {
    echo $twig->render("index.html.twig", [
        "authorized" => $authorized,
        "posts" => $db->getAllPosts($_POST['username'], $_POST['password'])
    ]);
} catch (LoaderError | RuntimeError | SyntaxError $e) {
    $logger->critical($e);
}

// TODO: TWIG CACHING
// TODO: LOGS TO VAR/LOG/...