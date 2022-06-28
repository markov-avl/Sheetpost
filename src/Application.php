<?php

namespace Sheetpost;

use Dotenv\Dotenv;
use Monolog\ErrorHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sheetpost\Model\Database\Adapters\DatabaseAdapterInterface;
use Sheetpost\Model\Database\Adapters\MySQLAdapter;
use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class Application
{
    private string $firstLevel = 'sheetpost-v4';
    private FormatterInterface $logFormatter;
    private HandlerInterface $logHandler;
    private LoggerInterface $logger;
    private LoaderInterface $twigLoader;
    private CacheInterface $twigCache;
    private Environment $twig;
    private DatabaseAdapterInterface $dbConnection;

    public function __construct()
    {
        Dotenv::createImmutable(dirname(__DIR__, 2))->load();

        $this->logFormatter = new LineFormatter($_ENV['LOGGER_OUTPUT_FORMAT'], $_ENV['LOGGER_DATE_FORMAT']);
        $this->logHandler = new StreamHandler($_ENV['LOGGER_PATH']);
        $this->logger = new Logger($_ENV['LOGGER_NAME']);
        $this->setLogger();

        $this->twigLoader = new FilesystemLoader($_ENV['TWIG_TEMPLATES_PATH']);
        $this->twigCache = new FilesystemCache($_ENV['TWIG_CACHE_PATH']);
        $this->twig = new Environment($this->twigLoader);
        $this->setTwig();

        $this->dbConnection = new MySQLAdapter(
            $_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']
        );
    }

    private function setLogger(): void
    {
        $this->logHandler->setFormatter($this->logFormatter);
        $this->logger->pushHandler($this->logHandler);
        ErrorHandler::register($this->logger);
    }

    private function setTwig(): void
    {
        if (intval($_ENV['TWIG_CACHING'])) {
            $this->twig->setCache($this->twigCache);
        }
    }

    private function deleteCookies(...$cookies): void
    {
        foreach ($cookies as $cookie) {
            setcookie($cookie, expires_or_options: -1, path: "/$this->firstLevel");
        }
    }

//    public function start(): void
//    {
//        $requestedPath = rtrim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');
//
//        // Если была отправлена форма на вход и правильно введены пользователь и пароль
//        if (str_ends_with($requestedPath, 'login')) {
//            if () {
//                setcookie('username', $_POST['username'], path: "/$this->firstLevel");
//                setcookie('password', $_POST['password'], path: "/$this->firstLevel");
//                header("Location: /$this->firstLevel/home");
//            } else {
//                header("Location: /$this->firstLevel");
//            }
//            die();
//        }
//
//        // Если была отправлена форма на выход
//        if (str_ends_with($requestedPath, 'logout')) {
//            $this->deleteCookies('username', 'password');
//            header("Location: /$this->firstLevel");
//            die();
//        }
//
//        $authorized = isset($_COOKIE['username'], $_COOKIE['password']) &&
//            UserRepository::getByFields(['username' => $_COOKIE['username'], 'password' => $_COOKIE['password']]) !== null;
//
//
//        // Перенаправление на главную страницу, если пользователь не авторизован (если были подменены значения кук)
//        if (!str_ends_with($requestedPath, $this->firstLevel) && !$authorized) {
//            $this->deleteCookies('username', 'password');
//            header("Location: /$this->firstLevel");
//            die();
//        }
//
//        // Перенаправление на главную страницу авторизованного пользователя, если он не находится сейчас на ней
//        if (str_ends_with($requestedPath, $this->firstLevel) && $authorized) {
//            header("Location: /$this->firstLevel/home");
//            die();
//        }
//
//        try {
//            $paths = explode('/', $requestedPath);
//            $template = end($paths);
//            if (isset($_COOKIE['username'])) {
//                if ($template === 'myposts') {
//                    $posts = ExtendedPostRepository::allUserPosts($_COOKIE['username']);
//                } elseif ($template === 'mysheets') {
//                    $posts = ExtendedPostRepository::allUserSheets($_COOKIE['username']);
//                } else {
//                    $posts = ExtendedPostRepository::allAuthorized($_COOKIE['username']);
//                }
//            } else {
//                $posts = ExtendedPostRepository::all();
//            }
//            echo $twig->render("$template.html.twig", [
//                "user" => $_COOKIE['username'] ?? null,
//                "posts" => $posts
//            ]);
//        } catch (LoaderError|RuntimeError|SyntaxError $e) {
//            $logger->critical($e);
//        }
//    }
}