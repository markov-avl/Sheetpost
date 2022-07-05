<?php

namespace Sheetpost;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Dotenv\Dotenv;
use Monolog\ErrorHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sheetpost\Controller\APIMethodController;
use Sheetpost\Controller\ControllerInterface;
use Sheetpost\Controller\PageController;
use Sheetpost\Model\Database\Configurations\ConfigurationAbstract;
use Sheetpost\Model\Database\Configurations\ProductionConfiguration;
use Sheetpost\Model\Database\Connections\ConnectionAbstract;
use Sheetpost\Model\Database\Connections\MySQLConnection;
use Sheetpost\Model\Database\Entities\User;
use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class Application
{
    private string $rootPath = 'sheetpost';
    private FormatterInterface $logFormatter;
    private HandlerInterface $logHandler;
    private LoggerInterface $logger;
    private LoaderInterface $twigLoader;
    private CacheInterface $twigCache;
    private Environment $twig;
    private ConnectionAbstract $dbConnection;
    private ConfigurationAbstract $ormConfiguration;
    private EntityManagerInterface $entityManager;
    private ?ControllerInterface $controller;

    /**
     * @throws ORMException
     */
    public function __construct()
    {
        Dotenv::createImmutable(dirname(__DIR__))->load();

        $this->logFormatter = new LineFormatter($_ENV['LOGGER_OUTPUT_FORMAT'], $_ENV['LOGGER_DATE_FORMAT']);
        $this->logHandler = new StreamHandler($_ENV['LOGGER_PATH']);
        $this->logger = new Logger($_ENV['LOGGER_NAME']);
        $this->setLogger();

        $this->twigLoader = new FilesystemLoader($_ENV['TWIG_TEMPLATES_PATH']);
        $this->twigCache = new FilesystemCache($_ENV['TWIG_CACHE_PATH']);
        $this->twig = new Environment($this->twigLoader);
        $this->setTwig();

        $this->dbConnection = new MySQLConnection(
            $_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']
        );
        $this->ormConfiguration = new ProductionConfiguration($_ENV['DOCTRINE_PROXY_PATH']);
        $this->entityManager = EntityManager::create(
            $this->dbConnection->toArray(),
            $this->ormConfiguration->toAnnotationMetadataConfiguration()
        );

        $this->controller = null;
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

    private function getUser(string $username, string $password): ?User
    {
        return $this->entityManager->getRepository(User::class)->findByUsernameAndPassword($username, $password);
    }

    private function setCookies(array $cookies): void
    {
        foreach ($cookies as $cookieName => $cookieValue) {
            setcookie($cookieName, $cookieValue, path: "/$this->rootPath");
        }
    }

    private function deleteCookies(...$cookies): void
    {
        foreach ($cookies as $cookie) {
            setcookie($cookie, expires_or_options: -1, path: "/$this->rootPath");
        }
    }

    private function setLocation(string $location): void
    {
        header("Location: /$location");
    }

    public function run(): void
    {
        $requestedPath = trim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');

        // Если была отправлена форма на выход
        if ($requestedPath === "$this->rootPath/logout") {
            $this->deleteCookies('username', 'password');
            $this->setLocation($this->rootPath);
            die();
        }

        // Если была отправлена форма на вход и правильно введены пользователь и пароль
        if ($requestedPath === "$this->rootPath/login") {
            if (isset($_POST['username'], $_POST['password'])
                && $this->getUser($_POST['username'], $_POST['password'])) {
                $this->setCookies(['username' => $_POST['username'], 'password' => $_POST['password']]);
                $this->setLocation("$this->rootPath/home");
            } else {
                $this->setLocation($this->rootPath);
            }
            die();
        }

        $user = isset($_COOKIE['username'], $_COOKIE['password']) ?
            $this->getUser($_COOKIE['username'], $_COOKIE['password']) : null;

        // Перенаправление на главную страницу, если пользователь значения кук были подменены
        if (preg_match("/$this->rootPath\/(home|myposts|mysheets)/", $requestedPath) && $user === null) {
            $this->deleteCookies('username', 'password');
            $this->setLocation($this->rootPath);
            die();
        }

        // Перенаправление на домашнюю страницу авторизованного пользователя, если он сейчас находится на главной
        if ($requestedPath === $this->rootPath && $user !== null) {
            $this->setLocation("$this->rootPath/home");
            die();
        }

        // Случаи, когда пользователю нужно показать представления
        if (preg_match("/$this->rootPath\/api\/.+/", $requestedPath)) {
            $this->controller = new APIMethodController($this->entityManager, $requestedPath);
        } else {
            $this->controller = new PageController(
                $this->twig,
                $this->entityManager,
                $requestedPath,
                $this->rootPath,
                $user
            );
        }
        $this->controller->show();
    }
}