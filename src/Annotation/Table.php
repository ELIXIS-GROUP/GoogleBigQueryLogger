<?php

namespace GoogleBigQueryLogger\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Declare property annotation "Table" for BigQuery service
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 * @package GoogleBigQueryLogger\Annotation
 * @version 1.0.0
 *
 * @Annotation
 * @Target("CLASS")
 **/
class Table
{

	public $name;

}
