<?php

/*
 * This file is part of the GooglBigQueryLogger package.
 * (c) Elixis Digital <support@elixis.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoogleBigQueryLogger\Annotation;

use Doctrine\Common\Annotations\Reader;
use GoogleBigQueryLogger\Annotation\Column as BigQueryColumn;
use GoogleBigQueryLogger\Annotation\Table as BigQueryTable;

/**
 * BigQuery Reader annotation, use Doctrine annotation for read annotation in entity file.
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 * @version 1.0.0
 **/
class BigQueryReader
{
    /**
     * @var Reader
     **/
    private $_reader;

    /**
     * @var BigQueryTable $_annotationTable
     **/
    private $_annotationTable;

    /**
     * @var array $_annotationColumn
     **/
    private $_annotationColumn;

    public function __construct(Reader $reader)
    {
        $this->_reader = $reader;
    }

    /**
     * Read annotation "BigQuery\Table".
     *
     * @param string $classEntity
     * @throws \ReflectionException
     * @since 1.0.0
     */
    public function tableAnnotation(string $classEntity): void
    {
        $reflection = new \ReflectionClass($classEntity);

        $classProperty = $this->_reader->getClassAnnotation($reflection, BigQueryTable::class);
        if ($classProperty === null)
        {
            // TODO something
        }
        $this->setAnnotationTable($classProperty);
    }

    /**
     * Read annotation "BigQuery\Columns".
     *
     * @param string $classEntity
     * @throws \ReflectionException
     * @since 1.0.0
     */
    public function columnsAnnotation(string $classEntity): void
    {
        $reflection = new \ReflectionClass($classEntity);
        $annotation = [];

        foreach ($reflection->getProperties() as $property) {
            $columnProperty = $this->_reader->getPropertyAnnotation($property, BigQueryColumn::class);

            if ($columnProperty) {
                $annotation[] = $columnProperty;
            }
        }

        $this->setAnnotationColumn($annotation);
    }

    /**
     * Set annotation "BigQuery\Table".
     *
     * @param BigQueryTable $annotationTable (BigQueryTable)
     * @return BigQueryTable
     * @since 1.0.0
     **/
    public function setAnnotationTable(BigQueryTable $annotationTable): BigQueryTable
    {
        return $this->_annotationTable = $annotationTable;
    }

    /**
     * Get annotation "BigQuery\Table".
     *
     * @return BigQueryTable
     * @since 1.0.0
     **/
    public function getAnnotationTable(): BigQueryTable
    {
        return $this->_annotationTable;
    }

    /**
     * Set annotation "BigQuery\Column".
     *
     * @param $annotationColumn (array)
     * @return array
     * @since 1.0.0
     **/
    public function setAnnotationColumn(array $annotationColumn): array
    {
        return $this->_annotationColumn = $annotationColumn;
    }

    /**
     * Get annotation "BigQuery\Column".
     *
     * @return array
     * @since 1.0.0
     **/
    public function getAnnotationColumn(): array
    {
        return $this->_annotationColumn;
    }
}
