<?php

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

class Application
{
    private ServiceLocator $serviceLocator;

    public function __construct()
    {
        $this->serviceLocator = $this->initServices();
    }

    private function initServices(): ServiceLocator
    {
        $serviceLocator = new ServiceLocator();
        $serviceLocator->set('pdo', new PDO('mysql:host=' . $_ENV['HOST']
            . ';dbname=' . $_ENV['DB_NAME'] . ';port='
            . $_ENV['PORT'],
            $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']));

        $serviceLocator->set("userRepository", function (ServiceLocator $locator) {
            return new UserRepository($locator->get('pdo'));
        });

        $serviceLocator->set("messageRepository", function (ServiceLocator $locator) {
            return new MessageRepository($locator->get('pdo'), $locator->get('userRepository'));
        });

        $serviceLocator->set('userController', function (ServiceLocator $locator) {
            return new UserController($locator->get('userRepository'));
        });

        $serviceLocator->set('homeController', function (ServiceLocator $locator) {
            return new HomeController($locator->get('messageRepository'), $locator->get('userRepository'));
        });

        $serviceLocator->set('errorController', new ErrorController());

        return $serviceLocator;
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);

        // костыль, потому что я уже устал
        $code = get_headers("https://avatars.dicebear.com/api/avataaars/" .
            $_COOKIE['login'] . ".svg?background=%23e6e6fa&size=40&radius=50");

        if (!stripos($code[0], '200')) {
            $notFoundController = $this->serviceLocator->get('errorController');
            echo $notFoundController->show("Not found", 404);
            exit();
        }
        // костыль закончился

        if ($uri['path'] === '/ModifyChat/home') {
            $homeController = $this->serviceLocator->get('homeController');

            // отправка сообщения
            if (isset($_POST['message'])) {
                $message = [
                    'text' => $_POST['message'],
                    'username' => $_COOKIE['login'],
                    'time' => date("Y-m-d H:i:s")
                ];
                $homeController->create($message);

                header('Location: ' . $_SERVER['REQUEST_URI']);

                $userController = $this->serviceLocator->get('userController');
                echo $homeController->show($userController->getLogo($_COOKIE['login']));
                die();

                // регистрация
            } elseif (isset($_POST['login']) and isset($_POST['password']) and isset($_POST['re-password'])) {
                $userController = $this->serviceLocator->get('userController');
                $userController->signUp([
                    'username' => $_POST['login'],
                    'password' => $_POST['password'],
                    're-password' => $_POST['re-password']
                ]);

            } elseif (isset($_POST['login']) and isset($_POST['password'])) {
                $userController = $this->serviceLocator->get('userController');

                $userController->logIn([
                    'username' => $_POST['login'],
                    'password' => $_POST['password']
                ]);

                // выйти из аккаунта
            } elseif ($_GET['logout'] == '1') {
                $userController = $this->serviceLocator->get('userController');
                $userController->logOut();
            }

            $userController = $this->serviceLocator->get('userController');
            echo $homeController->show($userController->getLogo($_COOKIE['login']));

        } elseif ($uri['path'] === '/ModifyChat/profile' and $_COOKIE['login']) {
            $userController = $this->serviceLocator->get('userController');

            // тут должен быть контроллер с редактированием
            if (isset($_POST['login']) and isset($_POST['old-password']) and isset($_POST['id'])) {
                $userController = $this->serviceLocator->get('userController');

                $userController->editProfile([
                    'username' => $_POST['login'],
                    'old-password' => $_POST['old-password'],
                    'new-password' => $_POST['new-password'],
                    'id' => $_POST['id']
                ]);
            }

            $userController->showProfile();

        } else {
            $notFoundController = $this->serviceLocator->get('errorController');
            echo $notFoundController->show("Not found", 404);
        }

        if (isset($_COOKIE['typeNoty'])) {
            echo "<script> displayNotification('{$_COOKIE['messageNoty']}', '{$_COOKIE['typeNoty']}') </script>";
        }
    }
}