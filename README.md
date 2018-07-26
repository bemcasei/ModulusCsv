# ModulusCsv

Export and import CSV files.

## Installation

```sh
php composer require bemcasei/modulus-csv
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