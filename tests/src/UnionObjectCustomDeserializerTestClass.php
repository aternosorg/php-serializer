<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class UnionObjectCustomDeserializerTestClass
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public string|SecondTestClass $value;

    public function getValue(): string|SecondTestClass
    {
        return $this->value;
    }
}
