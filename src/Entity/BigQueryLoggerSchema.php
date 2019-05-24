<?php

namespace GoogleBigQueryLogger\Entity;

use GoogleBigQueryLogger\Annotation as BigQuery;

/**
 * Data table schema to created in a BigQuery dataset.
 * Default schema use log message structure of a Monolog/logger.
 * @link https://github.com/Seldaek/monolog/blob/334b8d8783a1262c3b8311d6599889d82e9cc58c/doc/message-structure.md
 *
 * @author Anthony Papillaud <apapillaud@elxis.com>
 * @package GoogleBigQueryLogger
 * @subpackage Entity
 * @since 1.0.0
 * @version 1.0.0
 *
 * @BigQuery\Table(name="logger")
 **/
class BigQueryLoggerSchema
{

	/**
	 * @var string $message|null
	 * @BigQuery\Column(name="message", type="string")
	 **/
	public $message = null;

	/**
	 * @var string $level|null
	 * @BigQuery\Column(name="level", type="integer")
	**/
	public $level = null;

	/**
	 * @var string $levelName|null
	 * @BigQuery\Column(name="levelName", type="string")
	**/
	public $levelName = null;

	/**
	 * @var string $context|null
	 * @BigQuery\Column(name="context", type="string", nullable="true")
	**/
	public $context = null;

	/**
	 * @var string $channel|null
	 * @BigQuery\Column(name="channel", type="string")
	**/
	public $channel = null;

	/**
	 * @var string $datetime|null
	 * @BigQuery\Column(name="datetime", type="datetime")
	 **/
	public $datetime = null;

	public function setMessage(String $message): String
	{
		return $this->message = $message;
	}

	public function getMessage(): ?String
	{
		return $this->message;
	}

	public function setLevel(Int $level): Int
	{
		return $this->level = $level;
	}

	public function getLevel(): ?Int
	{
		return $this->level;
	}

	public function setLevelName(String $levelName): String
	{
		return $this->levelName = $levelName;
	}

	public function getLevelName(): ?String
	{
		return $this->levelName;
	}

	public function setContext(String $context): String
	{
		return $this->context = $context;
	}

	public function getContext(): ?String
	{
		return $this->context;
	}

	public function setChannel(String $channel): String
	{
		return $this->channel = $channel;
	}

	public function getChannel(): ?String
	{
		return $this->channel;
	}

	public function setDatetime(\DateTime $datetime): \DateTime
	{
		return $this->datetime = $datetime;
	}

	public function getDatetime(): ?\DateTime
	{
		return $this->datetime;
	}
}
