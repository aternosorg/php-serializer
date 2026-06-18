<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Iterator;
use Throwable;

class UnionIntersectionModel
{
    #[Serialize]
    protected bool|(Throwable&Iterator) $x;
}
