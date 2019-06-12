<?php

/*
 * This file is part of the GooglBigQueryLogger package.
 * (c) Elixis Digital <support@elixis.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoogleBigQueryLogger\Handler;

use Google\Cloud\BigQuery\BigQueryClient;
use GoogleBigQueryLogger\BigQueryTable;
use GoogleBigQueryLogger\QueryBuilder;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Extends class Monolog\Handler\AbstractProcessingHandler.
 * @see https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/AbstractProcessingHandler.php
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 *
 * @method __construct()
 *
 * @since 1.0.0
 * @version 1.1.0
 **/
class BigQueryHandler extends AbstractProcessingHandler
{
    /**
     * @var string $_classEntityName
     **/
    private $_classEntityName;

    /**
     * @var QueryBuilder $_bigQueryQueryBuilder
     **/
    private $_bigQueryQueryBuilder;

    /**
     * @var BigQueryTable $_bigQueryTable
     **/
    private $_bigQueryTable;

    /**
     * BigQueryHandler constructor.
     *
     * @param BigQueryClient $bigQueryClient
     * @param int            $level
     * @param bool           $bubble
     * @param BigQueryTable  $bigQueryTable
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function __construct(BigQueryTable $bigQueryTable, $level = Logger::DEBUG, $bubble = true)
    {
        $this->_bigQueryQueryBuilder = new QueryBuilder(new AnnotationReader(), $bigQueryTable->getBigQueryClient());
        $this->_bigQueryTable = $bigQueryTable;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     * @todo try catch for method parse_ini_file
     * @param array $record
     * @since 1.0.0
     * @version 1.1.0
     **/
    protected function write(array $record): void
    {
        $classEntity = $this->_bigQueryTable->getEntity();
        $loggerEntity = new $classEntity();

        $loggerEntity->setMessage($record['message']);
        $loggerEntity->setLevel($record['level']);
        $loggerEntity->setLevelName($record['level_name']);
        $loggerEntity->setChannel($record['channel']);
        $loggerEntity->setDatetime($record['datetime']);

        $formatedContent = $this->_formattedContext($record['context'], $loggerEntity);

        $record['context'] = $formatedContent['recordContext'];
        $loggerEntity = $formatedContent['loggerEntity'];

        $excludeEnv = $this->_bigQueryTable->listExcludeEnv($_ENV['EXCLUDE_ENV']);

        if (!in_array($_ENV['APP_ENV'], $excludeEnv)) {
            $this->_bigQueryQueryBuilder->setReaderEntity($classEntity);
            $this->_bigQueryQueryBuilder->insert($this->_bigQueryTable->getDatasetTable())
                                        ->values([(array) $loggerEntity])
                                        ->getQuery()
                                        ->execute();
        }

        if (true == $_ENV['SHOW_RESULTS'] || in_array($_ENV['APP_ENV'], $excludeEnv)) {
            $lines = preg_split('{[\r\n]+}', rtrim((string) $record['formatted']));
            foreach ($lines as $line) {
                error_log($line, 4);
            }
        }
    }

    /**
     * Formatted record context before save data.
     *
     * @param  array $recordContext
     * @param  void  $loggerEntity
     * @return array
     * @since 1.0.0
     * @version 1.0.0
     **/
    private function _formattedContext(array $recordContext, $loggerEntity): array
    {
        if (isset($recordContext)) {
            foreach ($recordContext as $setterKey => $setterValue) {
                $setterMethod = 'set'.ucfirst($setterKey);

                if (method_exists($loggerEntity, $setterMethod)) {
                    $loggerEntity->{'set'.ucfirst($setterKey)}($setterValue);
                    unset($recordContext[$setterKey]);
                }
            }

            if (!empty($recordContext)) {
                $loggerEntity->setContext(json_encode($recordContext));
            }
        }

        return ['loggerEntity' => $loggerEntity, 'recordContext' => $recordContext];
    }
}
