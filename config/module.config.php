<?php

namespace ModulusCsv;

use ModulusCsv\Controller\Plugin\CsvExport;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controller_plugins' => [
        'aliases' => [
            'CsvExport' => CsvExport::class,
        ],
        'factories' => [
            CsvExport::class => InvokableFactory::class,
        ],
        'shared' => [
            'CsvExport' => false,
            CsvExport::class => false
        ],
    ],
];