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
 * $serializer = new Serializer(new TestClass());
 * $data = $serializer->serialize();
 * $json = json_encode($data);
 * </code>
 *
 * If a property is not initialized and is either marked as required or doesn't have a default value,
 * a SerializationMissingPropertyException will be thrown.
 * If a property is null and marked as not nullable, a SerializationIncorrectTypeException will be thrown.
 *
 * @see SerializationProperty
 * @see PropertyJsonSerializer
 */
class JsonSerializer extends Serializer
{
    /**
     * Serialize this object to a JSON string.
     * @return string the serialized object
     * @throws SerializationMissingPropertyException If a required property is not set.
     * @throws SerializationIncorrectTypeException If a non-nullable property is set to null.
     */
    public function serializeToJson(): string
    {
        return json_encode($this->serialize());
    }
}