<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationMissingPropertyException;
use Aternos\Serializer\Serializer;

/**
 * A class that serializes objects using the SerializationProperty attribute.
 *
 * Usage:
 * <code>
 * $serializer = new Serializer();
 * $json = $serializer->serializeToJson(new TestClass());
 * </code>
 *
 * @see Serializer
 * @see PropertyJsonSerializer
 */
class JsonSerializer extends Serializer
{
    /**
     * Serialize this object to a JSON string.
     * @param object $item the object to serialize
     * @return string the serialized object
     * @throws SerializationMissingPropertyException If a required property is not set.
     * @throws SerializationIncorrectTypeException If a non-nullable property is set to null.
     */
    public function serializeToJson(object $item): string
    {
        return json_encode($this->serialize($item));
    }
}