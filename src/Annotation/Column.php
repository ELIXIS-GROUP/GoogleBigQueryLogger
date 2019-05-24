<?php

namespace GoogleBigQueryLogger\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Declare property annotation "Column" for BigQuery service
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 * @package GoogleBigQueryLogger\Annotation
 * @version 1.0.0
 *
 * @Annotation
 * @Target("PROPERTY")
 **/
class Column
{

	public $name;

	public $type;

	public $nullable;

}
