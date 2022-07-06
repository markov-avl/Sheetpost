<?php

require_once join(DIRECTORY_SEPARATOR, ['vendor', 'autoload.php']);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Dotenv\Dotenv;
use Sheetpost\Model\Database\Configurations\ProductionConfiguration;
use Sheetpost\Model\Database\Connections\MySQLConnection;

Dotenv::createImmutable(__DIR__)->load();
$connection = new MySQLConnection(
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD']
);
$configuration = new ProductionConfiguration($_ENV['DOCTRINE_PROXY_PATH']);

try {
    $entityManager = EntityManager::create(
        $connection->toArray(),
        $configuration->toAnnotationMetadataConfiguration()
    );
    return ConsoleRunner::createHelperSet($entityManager);
} catch (ORMException $e) {
    echo 'ORM Exception';
}
