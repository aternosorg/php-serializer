<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Iterator;
use Throwable;

class IntersectionModel
{
    #[Serialize]
    protected Throwable&Iterator $x;
}
