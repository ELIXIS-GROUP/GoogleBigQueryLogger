<?php

/*
 * This file is part of the GooglBigQueryLogger package.
 * (c) Elixis Digital <support@elixis.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoogleBigQueryLogger;

use Google\Cloud\BigQuery\BigQueryClient;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Initialize and use BigQuery API to create a new BigQuery Client.
 * @see https://cloud.google.com/bigquery/docs/quickstarts/quickstart-client-libraries BigQuery documentation : Quickstart: Using Client Libraries.
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 *
 * @method                __construct()
 * @method BigQueryClient getBigQueryClient()
 * @method string         setDataset()
 * @method string         getDataset()
 *
 * @since 1.0.0
 * @version 1.1.0
 **/
class BigQueryLogger
{
    /**
     * @var BigQueryClient $_bigQueryClient
     **/
    private $_bigQueryClient;

    /**
     * @var string $_dataset
     **/
    private $_dataset;

    public function __construct()
    {
        $this->_loadDotEnv();

        $bigQueryClientConfig = ['keyFilePath' => \dirname(__DIR__).$_ENV['GOOGLE_CREDENTIALS']];
        $this->_bigQueryClient = new BigQueryClient($bigQueryClientConfig);
        $this->setDataset($_ENV['DATASET']);
    }

    /**
     * Get a BigQuery Client.
     *
     * @since 1.0.0
     * @version 1.0.0
     * @return BigQueryClient
     **/
    public function getBigQueryClient(): BigQueryClient
    {
        return $this->_bigQueryClient;
    }

    /**
     * Set a BigQuery dataset name.
     *
     * @since 1.0.0
     * @version 1.0.0
     * @return string
     * @param  ?String $dataset
     **/
    public function setDataset(?String $dataset): ?String
    {
        $this->_dataset = $dataset;

        return $this->_dataset;
    }

    /**
     * Get a BigQuery dataset name.
     *
     * @since 1.0.0
     * @version 1.0.0
     * @return string
     **/
    public function getDataset(): ?String
    {
        return $this->_dataset;
    }

    /**
     * List exclude environment in array.
     *
     * @since 1.1.0
     * @version 1.1.0
     * @return array
     * @param  string $excludeEnv
     **/
    public function listExcludeEnv(string $excludeEnv): array
    {
        $excludeEnv = preg_replace('/[[\] ]+/', '', $excludeEnv);
        $envList = ('' !== $excludeEnv) ? explode(',', $excludeEnv) : [];

        return $envList;
    }

    /**
     * Load dotEnv package.
     *
     * @return string
     * @throws \Exception
     * @since 1.1.0
     * @version 1.1.1
     */
    private function _loadDotEnv(): ?string
    {
        if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
            if (!class_exists(Dotenv::class)) {
                throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
            }
            (new Dotenv())->load(__DIR__.'/../.env');
        }

        if ((!isset($_SERVER['DATASET']) && !isset($_ENV['DATASET'])) || (empty($_SERVER['DATASET']) && empty($_ENV['DATASET']))) {
            throw new \Exception('Configuration error, for this project. A "DATASET" name is required, add DATASET=acme from a .env file', 1);
        }

        if ((!isset($_SERVER['GOOGLE_CREDENTIALS']) && !isset($_ENV['GOOGLE_CREDENTIALS'])) || (empty($_SERVER['GOOGLE_CREDENTIALS']) && empty($_ENV['GOOGLE_CREDENTIALS']))) {
            throw new \Exception('Configuration error, for this project. Give keyfile path for load the credentials. More information : https://cloud.google.com/bigquery/docs/authentication/service-account-file', 1);
        }

        $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';
    }
}
