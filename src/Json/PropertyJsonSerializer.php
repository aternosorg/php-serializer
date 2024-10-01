<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationMissingPropertyException;
use Aternos\Serializer\Serializer;

/**
 * A trait that implements JsonSerializable for classes that use the SerializationProperty attribute.
 *
 * Usage:
 * <code>
 * class TestClass implements \JsonSerializable
 * {
 *   use PropertyJsonSerializer;
 *
 *   #[SerializationProperty]
 *   protected string $name;
 * }
 * </code>
 *
 * @see SerializationProperty
 */
trait PropertyJsonSerializer
{
    /**
     * Serialize this object to an associative array for json_encode.
     * @throws SerializationMissingPropertyException If a required property is not set.
     * @throws SerializationIncorrectTypeException If a non-nullable property is set to null.
     */
    public function jsonSerialize(): array
    {
        return (new Serializer())->serialize($this);
    }
}