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
        date_default_timezone_set("Asia/Vladivostok");
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
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
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

    private function getUser(array $variables): ?User
    {
        if (isset($variables['username'], $variables['password'])) {
            $user = $this->entityManager->getRepository(User::class)->findByUsername($variables['username']);
        }
        return isset($user) && $user->authenticate($variables['password']) ? $user : null;
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

    /**
     * ???????? ???????? ???????????????????? ?????????? ???? ??????????
     */
    private function routeLogout(string $requestedPath): void
    {
        if ($requestedPath === "$this->rootPath/logout") {
            $this->deleteCookies('username', 'password');
            $this->setLocation($this->rootPath);
            die();
        }
    }

    /**
     * ???????? ???????? ???????????????????? ?????????? ???? ???????? ?? ?????????????????? ?????????????? ???????????????????????? ?? ????????????
     */
    private function routeLogin(string $requestedPath): void
    {
        if ($requestedPath === "$this->rootPath/login") {
            if ($this->getUser($_POST)) {
                $this->setCookies(['username' => $_POST['username'], 'password' => $_POST['password']]);
                $this->setLocation("$this->rootPath/home");
            } else {
                $this->setLocation($this->rootPath);
            }
            die();
        }
    }

    /**
     * ?????????????????????????????? ???? ?????????????? ????????????????, ???????? ???????????????????????? ???????????????? ?????? ???????? ??????????????????
     */
    private function routeWrongCookies(string $requestedPath, ?User $user): void
    {
        if (preg_match("/$this->rootPath\/(home|myposts|mysheets)/", $requestedPath) && $user === null) {
            $this->deleteCookies('username', 'password');
            $this->setLocation($this->rootPath);
            die();
        }
    }

    /**
     * ?????????????????????????????? ???? ???????????????? ???????????????? ?????????????????????????????? ????????????????????????, ???????? ???? ???????????? ?????????????????? ???? ??????????????
     */
    private function routeWrongHomePage(string $requestedPath, ?User $user): void
    {
        if ($requestedPath === $this->rootPath && $user !== null) {
            $this->setLocation("$this->rootPath/home");
            die();
        }
    }

    public function run(): void
    {
        $requestedPath = trim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');

        $this->routeLogout($requestedPath);
        $this->routeLogin($requestedPath);

        $user = $this->getUser($_COOKIE);

        $this->routeWrongCookies($requestedPath, $user);
        $this->routeWrongHomePage($requestedPath, $user);

        // ????????????, ?????????? ???????????????????????? ?????????? ???????????????? ??????????????????????????
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