<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class UnionBuiltinCustomDeserializerTestClass
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public string|int $value;
}
