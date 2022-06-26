<?php

require_once join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);
define('TWIG_CACHE_PATH', join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'var', 'cache']));
define('TEMPLATES_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates');


use Dotenv\Dotenv;
use Sheetpost\Database\Repositories\ExtendedPostRepository;
use Sheetpost\Database\Repositories\UserRepository;
use Sheetpost\Models\LoggerWrapper;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

Dotenv::createImmutable(dirname(__DIR__))->load();

$loader = new FilesystemLoader(TEMPLATES_PATH);
$twig = new Environment($loader, ['cache' => $_ENV['TWIG_CACHING'] ? TWIG_CACHE_PATH : false]);
$logger = new LoggerWrapper("Logger");


$requestedPath = explode('?', $_SERVER['REQUEST_URI'])[0];
$requestedPath = str_ends_with($requestedPath, '/') ? rtrim($requestedPath, '/') : $requestedPath;


// Если была отправлена форма на вход и правильно введены пользователь и пароль
if (str_ends_with($requestedPath, 'login')) {
    if (isset($_POST['username'], $_POST['password']) &&
        UserRepository::getByFields(['username' => $_POST['username'], 'password' => $_POST['password']]) !== null) {
        setcookie('username', $_POST['username'], path: '/sheetpost-v3');
        setcookie('password', $_POST['password'], path: '/sheetpost-v3');
        header('Location: /sheetpost-v3/home');
    } else {
        header('Location: /sheetpost-v3');
    }
    die();
}

// Если была отправлена форма на выход
if (str_ends_with($requestedPath, 'logout')) {
    setcookie('username', expires_or_options: -1, path: '/sheetpost-v3');
    setcookie('password', expires_or_options: -1, path: '/sheetpost-v3');
    header('Location: /sheetpost-v3');
    die();
}

$authorized = isset($_COOKIE['username'], $_COOKIE['password']) &&
    UserRepository::getByFields(['username' => $_COOKIE['username'], 'password' => $_COOKIE['password']]) !== null;


// Перенаправление на главную страницу, если пользователь не авторизован (если были подменены значения кук)
if (!str_ends_with($requestedPath, 'sheetpost-v3') && !$authorized) {
    setcookie('username', expires_or_options: -1, path: '/sheetpost-v3');
    setcookie('password', expires_or_options: -1, path: '/sheetpost-v3');
    header('Location: /sheetpost-v3');
    die();
}

// Перенаправление на главную страницу авторизованного пользователя, если он не находится сейчас на ней
if (str_ends_with($requestedPath, 'sheetpost-v3') && $authorized) {
    header('Location: /sheetpost-v3/home');
    die();
}

try {
    $paths = explode('/', $requestedPath);
    $template = end($paths);
    if (isset($_COOKIE['username'])) {
        if ($template === 'myposts') {
            $posts = ExtendedPostRepository::allUserPosts($_COOKIE['username']);
        } elseif ($template === 'mysheets') {
            $posts = ExtendedPostRepository::allUserSheets($_COOKIE['username']);
        } else {
            $posts = ExtendedPostRepository::allAuthorized($_COOKIE['username']);
        }
    } else {
        $posts = ExtendedPostRepository::all();
    }
    echo $twig->render("$template.html.twig", [
        "user" => $_COOKIE['username'] ?? null,
        "posts" => $posts
    ]);
} catch (LoaderError | RuntimeError | SyntaxError $e) {
    $logger->critical($e);
}