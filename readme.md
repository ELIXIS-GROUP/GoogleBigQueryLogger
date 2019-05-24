# Google BigQuery logger for PHP (V1)

GoogleBigQuery-logger is PHP client, who extends a Monolog handler ```Monolog\Handler\AbstractProcessingHandler``` for writing logs in a BigQuery dataset making use of Google's ```google/cloud-bigquery``` PHP client.


## Instalation
Google BigQuery logger will be available comming soon on [Packagist][1] and can be installed with [Composer][2]. Run this command:
```sh
composer require ...
```

## Usage
> **Note:** This first version of the Google BigQuery logger for PHP requires PHP 7.1 or greater.

Simple example to write logs.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use GoogleBigQueryLogger\BigQueryLogger;
use GoogleBigQueryLogger\BigQueryTable;
use GoogleBigQueryLogger\Handler\BigQueryHandler;
use Monolog\Logger;

//Create a new table in a dataset
$bigQueryTable = new BigQueryTable();
$bigQueryTable->createTable();

// Create the logger
$logger = new Logger('logger');
// Add a new BigQuery handler
$logger->pushHandler(new BigQueryHandler($bigQueryTable));

// You can now use your logger
$logger->info('With only age');
```
## Documentation

- [Configuration Instructions](doc/01-configuration.md)
- [Usage Instructions](doc/02-usage.md)
- [Extends class extendsBigQueryLoggerSchema](doc/03-extendsBigQueryLoggerSchema.md)

## License
Monolog is licensed under the MIT License

[1]: https://packagist.org/ "Packagist"
[2]: https://getcomposer.org/ "Composer"
