# Developer guide

BigQueryLogger is a open source project. If you want contribute code or patches, you can create a pull request, to provide a bug fix or to propose enhancements.

# Before update
Before make an update/patch, install the external dependencies used. To do so, install Composer and execute the following:

```
$ composer update
```

## Php tools
Before push a bug fix or enhancement it's a good practice to run tests locally before submitting a patch for inclusion, to check that you have not broken anything.

GoogleBigQuerryLoger use too a packages for fix a code standards, and a tool for discover bugs in your code!.    
Follow the next commands before make a PR.

For run phpstan, phpcsfixer, test unit execute this command from project directory

```
$ php vendor/bin/phpstan analyse src
$ php vendor/bin/php-cs-fixer fix
$ phpUnit
```

# Documentation

Please, use this list to document, your contribute.

| Q             | A   |
| ------------- |  -  |
| Branch?       |  -  |
| Bug fix?      |  -  |
| New feature?  |  -  |
| BC breaks?    |  -  |
| Deprecations? |  -  |
| Tests pass?   |  -  |
| Fixed tickets |  -  |
| License	    | MIT |
| Doc PR        |  -  |
