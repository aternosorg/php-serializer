<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class UntypedCustomDeserializerTestClass
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public $value;

    public function getValue(): mixed
    {
        return $this->value;
    }
}
