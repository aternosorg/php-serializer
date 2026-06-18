<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\ObjectDeserializer;

class UnionBuiltinCustomDeserializerModel
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public string|int $value;
}
