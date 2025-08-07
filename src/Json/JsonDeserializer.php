<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\ArrayDeserializer;
use Aternos\Serializer\DeserializerInterface;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\InvalidEnumBackingException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use InvalidArgumentException;
use JsonException;

/**
 * Deserializes JSON into objects using the Serialize attribute.
 *
 * Usage:
 * ```php
 * $deserializer = new JsonDeserializer(TestClass::class);
 * $object = $deserializer->deserialize('{"name":"test","age":18}');
 * ```
 *
 * @see Serialize
 * @template T
 */
class JsonDeserializer implements DeserializerInterface
{
    protected ArrayDeserializer $arrayDeserializer;

    /**
     * Create a deserializer for a class
     *
     * @param class-string<T> $class the class to deserialize into
     */
    public function __construct(
        protected string $class,
    )
    {
        $this->arrayDeserializer = new ArrayDeserializer($class);
    }

    /**
     * Deserialize the data into an object
     *
     * @return T
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws MissingPropertyException if a required property is missing
     * @throws UnsupportedTypeException if the type of the property is unsupported
     * @throws InvalidEnumBackingException if the target class is an enum, but the serialized data is not a valid backing value
     * @throws JsonException if the data is invalid json
     */
    public function deserialize(mixed $data, string $path = ""): object
    {
        if (is_string($data)) {
            $data = json_decode($data, true, flags: JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                throw new IncorrectTypeException(".", $this->class, $data);
            }
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be a string or an array.");
        }

        return $this->arrayDeserializer->deserialize($data, $path);
    }
}
