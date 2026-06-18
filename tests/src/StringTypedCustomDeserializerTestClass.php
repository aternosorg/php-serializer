<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class StringTypedCustomDeserializerTestClass
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public string $value;
}
