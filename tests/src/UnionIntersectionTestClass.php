<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class UnionIntersectionTestClass
{
    #[Serialize]
    protected bool|(\Throwable&\Iterator) $x;
}