<?php

require __DIR__ . '/vendor/autoload.php';

use GoogleBigQueryLogger\BigQueryLogger;
use GoogleBigQueryLogger\BigQueryTable;
use GoogleBigQueryLogger\Handler\BigQueryHandler;
use Monolog\Logger;

$bigQueryTable = new BigQueryTable();
$bigQueryTable->createTable();

$logger = new Logger('logger');
$logger->pushHandler(new BigQueryHandler($bigQueryTable));

$logger->info('With username and age', ["username" => "Biqette TEST", "age" => "31"]);
$logger->info('With only username', ["username" => "Biqette"]);
$logger->info('With only age', ["age" => "31"]);
