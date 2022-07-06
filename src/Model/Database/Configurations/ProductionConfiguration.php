<?php

namespace Sheetpost\Model\Database\Configurations;

class ProductionConfiguration extends ConfigurationAbstract
{
    public function __construct(string $proxyDir)
    {
        parent::__construct(false, $proxyDir, null);
    }
}