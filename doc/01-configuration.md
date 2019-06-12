# Configuration Instructions

Before run project you need configure this one. The new version 1.1.0 use symfony/dotenv package for configure project.

if you don't use a Symfony project add the following line after autoload and create file .env

```php
<?php
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__.'/../.env');
}

```

## Variable structure
| NAME                  | Type         | DESCRIPTION           |
| --------------------- | ------------ | --------------------- |
| APP_ENV           | String       | Define current environment for project |
| EXCLUDE_ENV[] | Array        | Logs created from a list environment are not saved in database. By default this list contains test/debug environment. exemple : EXCLUDE_ENV=["test", "debug"]|
| SHOW_RESULTS          | String       | Send error messages to the defined error handling routines. This option be used in test/debug env for verify the smooth running of the handler |
| DATASET               | String       | Name of BigQuery dataset used to writing logs |
| GOOGLE_CREDENTIALS           | String       | Project use a Service Account Key File, to connect to GCP services. For more informations, for create credentials file read a BigQuery documentation[^1] |

[^1]: [https://cloud.google.com/bigquery/docs/authentication/service-account-file](https://cloud.google.com/bigquery/docs/authentication/service-account-file)
