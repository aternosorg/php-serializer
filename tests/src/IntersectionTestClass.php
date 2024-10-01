<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\SerializationProperty;

class IntersectionTestClass
{
    #[SerializationProperty]
    protected \Throwable&\Iterator $x;
}