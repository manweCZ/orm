<?php

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace NextrasTests\Orm;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\ManyHasOne;


/**
 * @property-read string                 $type       {default thread}
 * @property      ManyHasOne|Comment[]   $comments   {1:m Comment::$thread}
 */
class Thread extends Entity
{
}
