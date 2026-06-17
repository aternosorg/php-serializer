<?php

namespace Aternos\Serializer;

use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\InvalidEnumBackingException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;

/**
 * @template T of object
 */
interface DeserializerInterface
{
    /**
     * Create a deserializer for a class
     *
     * @param class-string<T> $class the class to deserialize into
     */
    public function __construct(string $class);

    /**
     * Deserialize the data into an object
     *
     * @return T
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws MissingPropertyException if a required property is missing
     * @throws UnsupportedTypeException if the type of the property is unsupported
     * @throws InvalidEnumBackingException if the target class is an enum, but the serialized data is not a valid backing value
     */
    public function deserialize(mixed $data, string $path = ""): object;
}
