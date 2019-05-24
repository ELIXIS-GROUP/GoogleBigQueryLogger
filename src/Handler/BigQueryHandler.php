<?php

namespace GoogleBigQueryLogger\Handler;

use Google\Cloud\BigQuery\BigQueryClient;
use GoogleBigQueryLogger\Entity;
use GoogleBigQueryLogger\BigQueryTable;
use GoogleBigQueryLogger\QueryBuilder;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Extends class Monolog\Handler\AbstractProcessingHandler.
 * @link https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/AbstractProcessingHandler.php
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 * @package GoogleBigQueryLogger
 *
 * @method __construct()
 *
 * @since 1.0.0
 * @version 1.0.0
**/
class BigQueryHandler extends AbstractProcessingHandler
{

    /**
     * @var String $_classEntityName
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
     * @param int $level
     * @param bool $bubble
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
     * {@inheritDoc}
     * @todo try catch for method parse_ini_file
     * @param array $record
     * @since 1.0.0
     * @version 1.0.0
    **/
    protected function write(array $record): void
    {

        $dataConfig = parse_ini_file(dirname(dirname(__DIR__)) . "/config/googleBigQueryLogger.ini");

        if( !$dataConfig ){
            throw new \Exception("/config/googleBigQueryLogger.ini is not defined, please add config before run project", 1);
        }

        $classEntity  = $this->_bigQueryTable->getEntity();
        $loggerEntity = new $classEntity();

        $loggerEntity->setMessage($record["message"]);
        $loggerEntity->setLevel($record["level"]);
        $loggerEntity->setLevelName($record["level_name"]);
        $loggerEntity->setChannel($record["channel"]);
        $loggerEntity->setDatetime($record["datetime"]);

        $formatedContent = $this->_formattedContext($record["context"], $loggerEntity);

        $record["context"] = $formatedContent["recordContext"];
        $loggerEntity      = $formatedContent["loggerEntity"];

        if( !in_array($dataConfig["environment"], $dataConfig["exclude_environment"]) ){

            $this->_bigQueryQueryBuilder->setReaderEntity($classEntity);
            $this->_bigQueryQueryBuilder->insert($this->_bigQueryTable->getDatasetTable())
                                        ->values([(array)$loggerEntity])
                                        ->getQuery()
                                        ->execute();
        }

        if( $dataConfig["show_results"] == true || in_array($dataConfig["environment"], $dataConfig["exclude_environment"]) ){

            $lines = preg_split('{[\r\n]+}', rtrim( (string) $record['formatted'] ));
            foreach ($lines as $line) {
                error_log($line, 4);
            }

        }

    }

    /**
     * Formatted record context before save data.
     *
     * @param array $recordContext
     * @param void $loggerEntity
     * @return array
     * @since 1.0.0
     * @version 1.0.0
    **/
    private function _formattedContext(Array $recordContext, $loggerEntity): Array
    {

        if( isset($recordContext) ){

            foreach($recordContext as $setterKey => $setterValue){

                $setterMethod = "set". ucfirst($setterKey);

                if( method_exists($loggerEntity, $setterMethod) ){
                    $loggerEntity->{"set". ucfirst($setterKey)}($setterValue);
                    unset($recordContext[$setterKey]);
                }

            }

            if( !empty($recordContext) ){
                $loggerEntity->setContext(json_encode($recordContext));
            }

        }

        return ["loggerEntity" => $loggerEntity, "recordContext" => $recordContext];

    }

}
