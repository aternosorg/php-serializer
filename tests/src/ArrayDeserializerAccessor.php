<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\ArrayDeserializer;

class ArrayDeserializerAccessor extends ArrayDeserializer
{
    public function isBuiltInTypeValid(string $type, mixed $value, string $path): bool
    {
        return parent::isBuiltInTypeValid($type, $value, $path);
    }
}