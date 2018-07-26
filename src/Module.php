<?php

declare(strict_types=1);

namespace ModulusCsv;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}