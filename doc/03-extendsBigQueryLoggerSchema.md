# Extends class BigQueryLoggerSchema

If you want add new columns in a data table. You need extends the class ```GoogleBigQueryLogger\EntityBigQueryLoggerSchema```. Follow next exemple :

__/Entity/MyBigQueryLoggerSchema.php__

```php
<?php
namespace ACME\Entity;

use GoogleBigQueryLogger\Entity\BigQueryLoggerSchema;

/**
 * Extends class BigQueryLoggerSchema
**/
class MyBigQueryLoggerSchema extends BigQueryLoggerSchema{

    /**
     * @var string $username|null
     * @BigQuery\Column(name="username", type="string", nullable="true")
     **/
    public $username = null;

    public function setUsername(String $username): String
    {
        return $this->username = $username;
    }

    public function getUsername(): ?String
    {
        return $this->username;
    }

}
```

This script create a new column "username" in table when a new table is created.    
For create column this script use of Doctrine ```Doctrine/Annotation``` php client. Currently we use only two of optionals parameters available in ```Doctrine/Annotation```, *name=""* and *nullable=""*.

Pour créer une colonne, ce script
> **Warning** : Once table is created, it's not possible to add/remove a column if you want update schema, you need create new table

__/Myfile.php__

```php
<?php

use ...;

//Create a new table in a dataset
$bigQueryTable = new BigQueryTable();
$bigQueryTable->createTable('/Entity/MyBigQueryLoggerSchema.php');

...

// You can now use your logger
$logger->info('My logger is now ready', array("username" => "Anthony"));
```

To use your custom entity, add the file path to the method createTable. To pass your custom data when you create a log. You must use the "logging context" put in place in Monolog.   
To pass your custom data when you create a log. You must use the "logging context" put in place in Monolog. If you pass a value not defined in entity, this values is saved in context column. its possible to pass at the same time defined and undefined values

__LOGS__
```
$logger->info('My logger is now ready', array("username" => "Anthony"));
$logger->error('Create error log', array("username" => "Anthony", "ip" => "127.0.0.1"));
$logger->error('Create error log', array("ip" => "127.0.0.1"));
```
__RESULTS__

| username | message                | level | levelName | context            | channel | datetime            |
| -------- | ---------------------- | ----- | --------- | ------------------ | ------- | ------------------- |
| Anthony  | My logger is now ready | 200   | INFO      | NULL               | logger  | 2019-05-22T16:21:23 |
| Anthony  | My logger is now ready | 500   | ERROR     | {"ip":"127.0.0.1"} | logger  | 2019-05-22T16:21:23 |
| NULL     | My logger is now ready | 500   | ERROR     | {"ip":"127.0.0.1"} | logger  | 2019-05-22T16:21:23 |
