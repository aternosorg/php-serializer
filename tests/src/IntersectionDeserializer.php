<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\DeserializerInterface;

class IntersectionDeserializer implements DeserializerInterface
{

    public function __construct(protected string $class)
    {
    }

    public function deserialize(mixed $data, string $path = ""): object
    {
        return new $this->class;
    }
}
