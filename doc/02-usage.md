# Usage Instructions

## Use a Google big query logger

Here is a basic usage  to writing log in a BigQuery Dataset.

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
$logger->info('My logger is now ready');
```

This example, create dynamically a new data table in dataset, if table does not exist.   
For this basic ```usage GoogleBigQuery-logger``` create the columns based on the [Monolog message structure](https://github.com/Seldaek/monolog/blob/master/doc/message-structure.md).

If you want, it's possible to extends the class ```GoogleBigQueryLogger\EntityBigQueryLoggerSchema``` for create new columns more specifics at your project.

As it is a Monolog handler, GoogleBigQuery-logger use the methods "level", "channel" and "context" implement in Monolog. [See the usage of Monolog](https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md).

[See the the next parts to extends Entity class](#).
