<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\IntersectionDeserializer;
use Aternos\Serializer\Test\Src\ThrowableIterator;
use Iterator;
use Throwable;

class IntersectionCustomModel
{
    #[Serialize(deserializer: new IntersectionDeserializer(ThrowableIterator::class))]
    protected Throwable&Iterator $x;
}
