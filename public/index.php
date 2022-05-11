<?php

require_once join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);
define('TWIG_CACHE_PATH', join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'var', 'cache']));
define('TEMPLATES_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates');


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
$twig = new Environment($loader, ['cache' => TWIG_CACHE_PATH]);
$logger = new LoggerWrapper("Logger");
$db = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$db->connect();


$requestedPath = explode('?', $_SERVER['REQUEST_URI'])[0];
$requestedPath = str_ends_with($requestedPath, '/') ? rtrim($requestedPath, '/') : $requestedPath;


// Если была отправлена форма на вход и правильно введены пользователь и пароль
if (str_ends_with($requestedPath, 'login')) {
    if (isset($_POST['username'], $_POST['password']) && $db->isUserExists($_POST['username'], $_POST['password'])) {
        setcookie('username', $_POST['username'], path: '/sheetpost');
        setcookie('password', $_POST['password'], path: '/sheetpost');
        header('Location: /sheetpost/home');
    } else {
        header('Location: /sheetpost');
    }
    die();
}

// Если была отправлена форма на выход
if (str_ends_with($requestedPath, 'logout')) {
    setcookie('username', expires_or_options: -1, path: '/sheetpost');
    setcookie('password', expires_or_options: -1, path: '/sheetpost');
    header('Location: /sheetpost');
    die();
}

$authorized = isset($_COOKIE['username'], $_COOKIE['password']) &&
    $db->isUserExists($_COOKIE['username'], $_COOKIE['password']);


// Перенаправление на главную страницу, если пользователь не авторизован (если были подменены значения кук)
if (!str_ends_with($requestedPath, 'sheetpost') && !$authorized) {
    setcookie('username', expires_or_options: -1, path: '/sheetpost');
    setcookie('password', expires_or_options: -1, path: '/sheetpost');
    header('Location: /sheetpost');
    die();
}

// Перенаправление на главную страницу авторизованного пользователя, если он не находится сейчас на ней
if (str_ends_with($requestedPath, 'sheetpost') && $authorized) {
    header('Location: /sheetpost/home');
    die();
}

$logger->debug("HELLO!");

try {
    $paths = explode('/', $requestedPath);
    $template = end($paths);
    echo $twig->render("$template.html.twig", [
        "user" => $_COOKIE['username'] ?? null,
        "posts" => isset($_COOKIE['username']) && $template === 'myposts'
            ? $db->getUserPosts($_COOKIE['username'])
            : $db->getAllPosts($_COOKIE['username'] ?? null)
    ]);
} catch (LoaderError | RuntimeError | SyntaxError $e) {
    $logger->critical($e);
}

// TODO: TWIG CACHING
// TODO: LOGS TO VAR/LOG/...