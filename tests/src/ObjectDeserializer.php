<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\DeserializerInterface;

class ObjectDeserializer implements DeserializerInterface
{
    public function __construct(string $class = SecondTestClass::class)
    {
    }

    public function deserialize(mixed $data, string $path = ""): object
    {
        return new SecondTestClass();
    }
}
