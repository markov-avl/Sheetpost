<?php

namespace Sheetpost\Model\Database\Configurations;

class DevelopmentConfiguration extends ConfigurationAbstract
{
    public function __construct(string $proxyDir)
    {
        parent::__construct(true, $proxyDir, null);
    }
}