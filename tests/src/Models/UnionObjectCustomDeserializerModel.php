<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\ObjectDeserializer;

class UnionObjectCustomDeserializerModel
{
    #[Serialize(deserializer: new ObjectDeserializer())]
    public string|SecondModel $value;

    public function getValue(): string|SecondModel
    {
        return $this->value;
    }
}
