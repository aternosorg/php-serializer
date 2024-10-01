<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\SerializationProperty;

class UnionIntersectionTestClass
{
    #[SerializationProperty]
    protected bool|(\Throwable&\Iterator) $x;
}