<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class ObjectTypedCustomDeserializerTestClass
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public object $value;

    public function getValue(): object
    {
        return $this->value;
    }
}
