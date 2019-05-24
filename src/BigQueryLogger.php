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
 * @version 1.0.0
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
        $dotenv = new Dotenv();
        $dotenv->loadEnv($_SERVER['DOCUMENT_ROOT'].'/.env');

        $this->_setExcludeEnv($_ENV['EXCLUDE_ENV']);

        if (is_null($_ENV['DATASET'])) {
            throw new \Exception('Configuration error, for this project. a "dataset" name is required. Add dataset=acme in ini file', 1);
        }

        if (is_null($_ENV['GOOGLE_CREDENTIALS'])) {
            throw new \Exception('Configuration error, for this project. Give keyfile path for load the credentials. More information : https://cloud.google.com/bigquery/docs/authentication/service-account-file', 1);
        }

        $bigQueryClientConfig = ['keyFilePath' => dirname(__DIR__).$_ENV['GOOGLE_CREDENTIALS']];
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
     * Set env variable EXCLUDE_ENV with an array containted the environment list to exclude.
     *
     * @since 1.0.1
     * @version 1.0.1
     * @return string
     * @param  String $excludeEnv
     **/
    private function _setExcludeEnv(string $excludeEnv): array
    {
        $excludeEnv = preg_replace('/[[\] ]+/', '', $excludeEnv);
        $envList = explode(',', $excludeEnv);
        $_ENV['EXCLUDE_ENV'] = $envList;
    }
}
