<?php

namespace Aternos\Serializer;

interface SerializerInterface
{
    /**
     * Serialize an object into a scalar value or an array.
     *
     * @param object $item
     * @return int|float|string|array|null
     */
    public function serialize(object $item): int|float|string|array|null;
}
