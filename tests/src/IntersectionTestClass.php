<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class IntersectionTestClass
{
    #[Serialize]
    protected \Throwable&\Iterator $x;
}