<?php

namespace GoogleBigQueryLogger;

use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Connection;
use Google\Cloud\BigQuery\BigQueryClient;
use GoogleBigQueryLogger\Annotation\BigQueryReader as BigQueryReader;

/**
 * Create native query for INSERT/SELECT/UPDATE data in BigQuery
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 * @package GoogleBigQueryLogger
 *
 * @method __construct()
 * @method setReaderEntity()
 * @method add() QueryBuilder
 * @method setMaxResults() Integer
 * @method getMaxResults() Integer
 * @method setFirstResult() Integer
 * @method getFirstResult() Integer
 * @method select() QueryBuilder
 * @method insert() QueryBuilder
 * @method update() QueryBuilder
 * @method values() QueryBuilder
 * @method set() QueryBuilder
 * @method where() QueryBuilder
 * @method andWhere() QueryBuilder
 * @method groupBy() QueryBuilder
 * @method orderBy() QueryBuilder
 * @method getQuery() QueryBuilder
 * @method execute()
 *
 * @since 1.0.0
 * @version 1.0.0
**/
class QueryBuilder
{

	/**
     * @var String SELECT|DELETE|UPDATE|INSERT The query types
	**/
	const SELECT = 0;
	const DELETE = 1;
	const UPDATE = 2;
	const INSERT = 3;

	/**
     * @var String TYPE_AND|TYPE_OR AND or OR sql operator.
	**/
	const TYPE_AND = 'AND';
	const TYPE_OR  = 'OR';

	/**
	 * @var Array $sqlParts The array of SQL parts collected.
	**/
	private $sqlParts = [
		'select'  => [],
		'from'    => [],
		'join'    => [],
		'set'     => [],
		'where'   => null,
		'groupBy' => [],
		'having'  => null,
		'orderBy' => [],
		'values'  => [],
	];

	/**
	 * @var String $sql The complete SQL string for this query.
	**/
	private $sql;

    /**
	 * @var Int $_maxResults|null
	**/
	private $_maxResults = null;

    /**
	 * @var Int $_firstResult|null
	**/
	private $_firstResult = null;

	/**
	 * @var BigQueryClient $_bigQueryClient
	**/
	private $_bigQueryClient;

	/**
	 * @var Reader $_reader
	**/
	private $_reader;

	/**
	 * @var Int $type|SELECT The type of query this is. Can be select, update or delete.
	**/
	private $type = self::SELECT;

	public function __construct(Reader $reader, BigQueryClient $bigQueryClient)
	{

		$this->_reader 			= new BigQueryReader($reader);
		$this->_bigQueryClient  = $bigQueryClient;

	}

	/**
	 * Set entity class
	 *
	 * @param string $classEntity
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function setReaderEntity( String $classEntity )
	{

		$classEntity = new $classEntity;

		$this->_reader->columnsAnnotation(get_class($classEntity));
		$this->_reader->tableAnnotation(get_class($classEntity));

	}

	/**
	 * Either appends to or replaces a single, generic query part.
	 *
	 * @param string $sqlPartName
	 * @param string $sqlPart
	 * @param bool $append|false
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function add($sqlPartName, $sqlPart, $append = false): QueryBuilder
	{

		if ($append) {

			if( $sqlPartName == "set" || $sqlPartName == "groupBy" || $sqlPartName == "orderBy" ){
				$this->sqlParts[$sqlPartName][] = $sqlPart;
			}else{
				$this->sqlParts[$sqlPartName] = $sqlPart;
			}

			return $this;

		}

		$this->sqlParts[$sqlPartName] = $sqlPart;

		return $this;

	}

	/**
	 * Gets a query part by its name.
	 *
	 * @param string $queryPartName
	 * @return array
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function _getQueryPart(String $queryPartName): Array
	{
		return $this->sqlParts[$queryPartName];
	}

	/**
	 * Set query max results
	 *
	 * @param integer $maxResults
	 * @return integer
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function setMaxResults(Integer $maxResults): ?Integer
	{
		$this->_maxResults = $maxResults;
		return $this->_maxResults;
	}

	/**
	 * Get query max results
	 *
	 * @param integer $maxResults
	 * @return integer
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function getMaxResults(): ?Integer
	{
		return $this->_maxResults;
	}

	/**
	 * Set query first result
	 *
	 * @param integer $maxResults
	 * @return integer
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function setFirstResult(Integer $firstResult): ?Integer
	{
		$this->_firstResult = $firstResult;
		return $this->_firstResult;
	}

	/**
	 * Set query first result
	 *
	 * @param integer $maxResults
	 * @return integer
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function getFirstResult(): ?Integer
	{
		return $this->_firstResult;
	}

	/**
	 * Tests if the value of the first result is lower than the value of the max results
	 *
	 * @return bool
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	private function _isLimitQuery(): bool
	{
		$isLimit = ( $this->getMaxResults() !== null AND $this->getFirstResult() !== null )? true : false;
		return $isLimit;
	}


	/**
	 * Turns the query being built into a bulk update query that ranges over a certain table
	 *
	 * @param string $datasetTable
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function insert(String $datasetTable = null): QueryBuilder
	{

		$this->type = self::INSERT;

		return $this->add('from', [
			'table' => $datasetTable
		]);

	}

	/**
	 * Turns the query being built into a bulk update query that ranges over a certain table
	 *
	 * @param string $datasetTable
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function select(String $select = null): QueryBuilder
	{

		$this->type = self::SELECT;

		if (empty($select)) {
			return $this;
		}

		$selects = is_array($select) ? $select : func_get_args();

		return $this->add('select', $selects);

	}

	/**
	 * Turns the query being built into a bulk update query that ranges over a certain table
	 *
	 * @param string $datasetTable
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function update(String $datasetTable = null): QueryBuilder
	{

		$this->type = self::UPDATE;

		return $this->add('from', [
			'table' => $datasetTable
		]);

	}

	/**
	 * Create columns name for an insert query from entity, use annotaitions reader
	 *
	 * @return array
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	private function _getKeyValues(): Array
	{

		$columnsName = [];

		foreach ($this->_reader->getAnnotationColumn() as $annotationColumn) {
			array_push($columnsName, $annotationColumn->name);
		}

		return $columnsName;

	}

	/**
	 * Turns the "values" array into an insert query for bulk insertion
	 *
	 * @param array $values
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function values( Array $values ): QueryBuilder
	{

		$rows = [];

		foreach ($values as $key => $data) {

			$row = [];
			foreach ($this->_reader->getAnnotationColumn() as $annotationColumn){

				if( isset($data[$annotationColumn->name]) ){

					$value = ( $annotationColumn->type != "string" && $annotationColumn->type != "datetime" )? $data[$annotationColumn->name] : "'" . addslashes($data[$annotationColumn->name]) . "'";

					if( $annotationColumn->type === "datetime" )
						$value = "'" . $data[$annotationColumn->name]->format("Y-m-d H:i:s") . "'";

					if( $data[$annotationColumn->name] === "" || is_null($data[$annotationColumn->name]) )
						$value = "null";

				}else{
					$value = "null";
				}

				array_push($row, $value);

			}

			array_push($rows, '(' . implode(',', $row) . ')' );

		}

		return $this->add('values', $rows);

	}

	/**
	 * Sets a new value for a column in a bulk update query.
	 *
	 * @param string $key
	 * @param string $value
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function set(String $key, String $value): QueryBuilder
	{
		return $this->add('set', $key .' = "' . $value . '"', true);
	}

	/**
	 * Specifies one or more restrictions to the query result.
	 * Replaces any previously specified restrictions, if any.
	 *
	 * @param string $where
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function where(String $where): QueryBuilder
	{
		$where = ( $this->_getQueryPart('where') == "" ) ? $where : $this->_getQueryPart('where') . " " . self::TYPE_AND . " ". $where;
		return $this->add('where', $where);
	}

	/**
	 * Adds one or more restrictions to the query results, forming a logical
	 * conjunction with any previously specified restrictions.
	 *
	 * @param string $where
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function andWhere(String $where): QueryBuilder
	{
		$where = ( $this->_getQueryPart('where') == "" ) ? $where : $this->_getQueryPart('where') . " " . self::TYPE_AND . " ". $where;
		return $this->add('where', $where, true);
	}

	/**
	 * Specifies an "groupby" field for the query results.
	 *
	 * @param string|array $groupBy
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function groupBy($groupBy): QueryBuilder
	{
		if (empty($groupBy)) {
			return $this;
		}

		$groupBy = is_array($groupBy) ? $groupBy : func_get_args();

		return $this->add('groupBy', $groupBy, false);
	}

	/**
	 * Specifies an ordering for the query results.
	 *
	 * @param string $orderBy
	 * @param string $order|null
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function orderBy(String $orderBy, String $order = null): QueryBuilder
	{
		return $this->add('orderBy', $orderBy  . ' ' . (! $order ? 'ASC' : $order), false);
	}

	/**
	 * Gets the complete SQL string formed by the current specifications of this QueryBuilder.
	 *
	 * @return QueryBuilder
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function getQuery(): QueryBuilder
	{

		$sql = "";

		switch ($this->type){
			case self::UPDATE:
				$sql = $this->_getSQLForUpdate();
				break;
			case self::INSERT:
				$sql = $this->_getSQLForInsert();
				break;
			case self::SELECT:
				$sql = $this->_getSQLForSelect();
				break;
		}

		$this->sql = $sql;

		return $this;

	}

	/**
	 * Converts this instance into an UPDATE string in SQL.
	 *
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	private function _getSQLForUpdate()
	{

		$query = "UPDATE " . $this->sqlParts['from']['table']
			. ' SET ' . implode(", ", $this->sqlParts['set'])
			. ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');

		return $query;

	}

	/**
	 * Converts this instance into an INSERT string in SQL.
	 *
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	private function _getSQLForInsert()
	{

		$query = "INSERT INTO " . $this->sqlParts['from']['table']
			. ' ( ' . implode(", ", $this->_getKeyValues()) . ' )'
			. ' VALUES ' . implode(', ', $this->sqlParts['values']);

		return $query;

	}

	/**
	 * Converts this instance into an SELECT string in SQL.
	 *
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	private function _getSQLForSelect(): String
	{

		$query = "SELECT " . implode(', ', $this->sqlParts['select'])
			. ' FROM ' . $this->sqlParts['from']['table']
			. ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '')
			. ( count($this->sqlParts['groupBy']) !== 0 ? ' GROUP BY ' . implode(', ', $this->sqlParts['groupBy']) : '')
			. ($this->sqlParts['orderBy'] !== null ? ' ORDER BY ' . ((string) $this->sqlParts['orderBy']) : '');

		if ($this->_isLimitQuery()) {
			return $this->connection->getDatabasePlatform()->modifyLimitQuery(
				$query,
				$this->maxResults,
				$this->firstResult
			);
		}

		return $query;

	}

	/**
	 * Execute sql query in a BigQueryClient
	 *
	 * @return \Google\Cloud\Core\Iterator\ItemIterator
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function execute()
	{

		$this->sqlParts = [
			'select'  => [],
			'from'    => [],
			'join'    => [],
			'set'     => [],
			'where'   => null,
			'groupBy' => [],
			'having'  => null,
			'orderBy' => [],
			'values'  => [],
		];

		$jobConfig 	  = $this->_bigQueryClient->query( $this->sql )->useLegacySql(false);
		$queryResults = $this->_bigQueryClient->runQuery($jobConfig);

		if ($queryResults->isComplete()) {
			return $queryResults->rows();
		}else{
			throw new Exception('The query failed to complete');
		}

	}

}
