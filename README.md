# ModulusCsv

Export and import CSV files.

## Installation

```sh
composer require bemcasei/modulus-csv
```

### Enable module 
Register as Zend Framework module inside your ```config/application.config.php``` file:
```php
// config/application.config.php
return [
    'modules' => [
        'ModulusCsv',
    ]
],
```