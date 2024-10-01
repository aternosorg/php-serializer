<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\Deserializer;
use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationMissingPropertyException;
use Aternos\Serializer\SerializationUnsupportedTypeException;
use JsonException;

/**
 * Class Deserializer
 *
 * Deserializes JSON into objects using the SerializationProperty attribute.
 *
 * Usage:
 * ```php
 * $deserializer = new JsonDeserializer(TestClass::class);
 * $object = $deserializer->deserialize('{"name":"test","age":18}');
 * ```
 *
 * @see SerializationProperty
 * @template T
 */
class JsonDeserializer extends Deserializer
{
    /**
     * Deserialize the data into an object
     *
     * @return T
     * @throws SerializationIncorrectTypeException if the type of the property is incorrect
     * @throws SerializationMissingPropertyException if a required property is missing
     * @throws SerializationUnsupportedTypeException if the type of the property is unsupported
     * @throws JsonException if the data is invalid json
     */
    public function deserialize(array|string $data, string $path = ""): object
    {
        if (is_string($data)) {
            $data = json_decode($data, true, flags: JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw new SerializationIncorrectTypeException(".", $this->class, $data);
            }
        }

        return parent::deserialize($data, $path);
    }
}