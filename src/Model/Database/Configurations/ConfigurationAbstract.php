<?php

namespace Sheetpost\Model\Database\Configurations;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;

abstract class ConfigurationAbstract
{
    private bool $isDevMode;
    private ?string $proxyDir;
    private ?Cache $cache;

    public function __construct(bool $isDevMode, ?string $proxyDir, ?Cache $cache)
    {
        $this->isDevMode = $isDevMode;
        $this->proxyDir = $proxyDir;
        $this->cache = $cache;
    }

    public function toAnnotationMetadataConfiguration(): Configuration
    {
        return Setup::createAnnotationMetadataConfiguration(
            [dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Entities'],
            $this->isDevMode,
            $this->proxyDir,
            $this->cache,
            false
        );
    }
}