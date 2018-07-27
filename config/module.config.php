<?php

namespace ModulusCsv;

use ModulusCsv\Controller\Plugin\CsvExport;
use ModulusCsv\Controller\Plugin\CsvImport;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controller_plugins' => [
        'aliases' => [
            'CsvExport' => CsvExport::class,
            'CsvImport' => CsvImport::class,
        ],
        'factories' => [
            CsvExport::class => InvokableFactory::class,
            CsvImport::class => InvokableFactory::class,
        ],
        'shared' => [
            'CsvExport' => false,
            'CsvImport' => false,
            CsvExport::class => false,
            CsvImport::class => false
        ],
    ],
];