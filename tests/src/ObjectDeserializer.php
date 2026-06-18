<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\DeserializerInterface;
use Aternos\Serializer\Test\Src\Models\SecondModel;

class ObjectDeserializer implements DeserializerInterface
{
    public function __construct(string $class = SecondModel::class)
    {
    }

    public function deserialize(mixed $data, string $path = ""): object
    {
        return new SecondModel();
    }
}
