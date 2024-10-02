<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;
use Iterator;
use Throwable;

class IntersectionTestClass
{
    #[Serialize]
    protected Throwable&Iterator $x;
}