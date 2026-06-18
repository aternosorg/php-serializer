<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\ObjectDeserializer;

class UntypedCustomDeserializerModel
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public $value;

    public function getValue(): mixed
    {
        return $this->value;
    }
}
