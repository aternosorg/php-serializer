<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\ArrayDeserializer;
use ReflectionType;

class ArrayDeserializerAccessor extends ArrayDeserializer
{
    public function isBuiltInTypeValid(string $type, mixed $value, string $path): bool
    {
        return parent::isBuiltInTypeValid($type, $value, $path);
    }

    public function isTypeValid(?ReflectionType $type, mixed $value, string $path): bool
    {
        return parent::isTypeValid($type, $value, $path);
    }
}
