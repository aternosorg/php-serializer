<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\ObjectDeserializer;

class ObjectTypedCustomDeserializerModel
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public object $value;

    public function getValue(): object
    {
        return $this->value;
    }
}
