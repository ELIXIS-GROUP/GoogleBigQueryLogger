<?php

/*
 * This file is part of the GooglBigQueryLogger package.
 * (c) Elixis Digital <support@elixis.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoogleBigQueryLogger\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Declare property annotation "Column" for BigQuery service.
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
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
