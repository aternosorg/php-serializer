<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;
use Iterator;
use Throwable;

class UnionIntersectionTestClass
{
    #[Serialize]
    protected bool|(Throwable&Iterator) $x;
}