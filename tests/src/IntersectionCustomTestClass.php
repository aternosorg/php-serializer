<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;
use Iterator;
use Throwable;

class IntersectionCustomTestClass
{
    #[Serialize(deserializer: new IntersectionDeserializer(ThrowableIterator::class))]
    protected Throwable&Iterator $x;
}
