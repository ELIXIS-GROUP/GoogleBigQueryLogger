<?php

/*
 * This file is part of the GooglBigQueryLogger package.
 * (c) Elixis Digital <support@elixis.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoogleBigQueryLogger;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Google\Cloud\BigQuery\BigQueryClient;
use GoogleBigQueryLogger\Annotation\BigQueryReader;
use GoogleBigQueryLogger\Entity\BigQueryLoggerSchema;

/**
 * BigQueryLogger create data table, in a BigQuery dataset.
 * @see https://cloud.google.com/bigquery/docs/tables BigQuery documentation : Creating and using tables.
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 *
 * @method        __construct()
 * @method        setEntity()
 * @method string getTableName()
 * @method string setTableSuffix()
 * @method string getTableSuffix()
 * @method array  getBigQueryTables()
 *
 * @since 1.0.0
 * @version 1.0.0
 **/
class BigQueryTable extends BigQueryLogger
{
    /**
     * @var string $_bigQueryDataset
     **/
    private $_bigQueryDataset;

    /**
     * @var BigQueryReader $_bigQueryReader
     **/
    private $_bigQueryReader;

    /**
     * @var BigQueryClient $_bigQueryClient
     **/
    private $_bigQueryClient;

    /**
     * @var string $_entityName
     **/
    private $_entityName;

    /**
     * @var string $_tableSuffix
     **/
    private $_tableSuffix;

    /**
     * @var string $_datasetTable
     **/
    private $_datasetTable;

    public function __construct()
    {
        parent::__construct();

        //FIXME : registerFile deprecated
        AnnotationRegistry::registerFile(dirname(__DIR__).'/src/Annotation/Column.php');
        AnnotationRegistry::registerFile(dirname(__DIR__).'/src/Annotation/Table.php');

        $this->_bigQueryReader = new BigQueryReader(new AnnotationReader());
        $this->_bigQueryClient = parent::getBigQueryClient();
        $this->_bigQueryDataset = parent::getDataset();
    }

    /**
     * Create new table in the BigQuer dataset
     * Schema fields is specify from the entity annotations.
     *
     * @param string $classEntity |GoogleBigQueryLogger\Entity\BigQueryLoggerSchema
     * @param bool $autoCreated |true
     * @throws \ReflectionException
     * @since 1.0.0
     * @version 1.0.0
     */
    public function createTable(string $classEntity = BigQueryLoggerSchema::class, bool $autoCreated = true): void
    {
        $fields = [];
        $this->_bigQueryReader->columnsAnnotation(get_class(new $classEntity()));
        $this->_bigQueryReader->tableAnnotation(get_class(new $classEntity()));
        $this->setEntity($classEntity);

        $tableName = $this->_bigQueryReader->getAnnotationTable()->name;
        $columns = $this->_bigQueryReader->getAnnotationColumn();

        if (!$this->_tableExist($tableName) && $autoCreated) {
            if ($this->getTableSuffix() !== null) {
                $tableName = $tableName.'_'.$this->getTableSuffix();
            }

            //Create table schema
            foreach ($columns as $key => $field) {
                if (!$field->nullable) {
                    $field->mode = 'required';
                }

                unset($field->nullable);

                $fields[] = (array)$field;
            }

            $dataset = $this->_bigQueryClient->dataset($this->_bigQueryDataset);

            $dataset->createTable($tableName, ['schema' => ['fields' => $fields]]);
        }

        $this->setDatasetTable(sprintf('%s.%s', $this->_bigQueryDataset, $tableName));
    }

    /**
     * Set entity name.
     *
     * @param  string|null $entityName
     * @return string
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function setEntity(?String $entityName): ?String
    {
        $this->_entityName = $entityName;

        return $this->_entityName;
    }

    /**
     * Get entity name.
     *
     * @return string
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function getEntity(): ?String
    {
        return $this->_entityName;
    }

    /**
     * Set tableSuffix.
     *
     * @param  string|null $tableSuffix
     * @return string|null
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function setTableSuffix(?String $tableSuffix): ?String
    {
        $this->_tableSuffix = $tableSuffix;

        return $this->_tableSuffix;
    }

    /**
     * Get tableSuffix.
     *
     * @return string|null
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function getTableSuffix(): ?String
    {
        return $this->_tableSuffix;
    }

    /**
     * Set datasetTable.
     *
     * @param  string $datasetTable
     * @return string
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function setDatasetTable(string $datasetTable): string
    {
        $this->_datasetTable = $datasetTable;

        return $this->_datasetTable;
    }

    /**
     * Get datasetTable.
     *
     * @return string|null
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function getDatasetTable(): ?String
    {
        return $this->_datasetTable;
    }

    /**
     * Check if table exists.
     *
     * @param  string $tableName
     * @return bool
     * @since 1.0.0
     * @version 1.0.0
     **/
    private function _tableExist(string $tableName): bool
    {
        $tableName = ($this->getTableSuffix()) ? $tableName.'_'.$this->getTableSuffix() : $tableName;

        return in_array($tableName, $this->getBigQueryTables($this->_bigQueryDataset));
    }

    /**
     * Return list of tables create in a BigQuery dataset.
     *
     * @param  string $datasetName
     * @return array
     * @since 1.0.0
     * @version 1.0.0
     **/
    public function getBigQueryTables(string $datasetName): array
    {
        $dataset = $this->_bigQueryClient->dataset($datasetName);
        $tables = $dataset->tables();
        $tablesID = [];

        foreach ($tables as $table) {
            $tablesID[] = $table->id();
        }

        return $tablesID;
    }
}
