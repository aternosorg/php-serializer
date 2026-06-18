<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\ObjectDeserializer;

class StringTypedCustomDeserializerModel
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public string $value;
}
