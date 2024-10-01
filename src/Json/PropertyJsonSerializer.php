<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Exceptions\SerializationIncorrectTypeException;
use Aternos\Serializer\Exceptions\SerializationMissingPropertyException;

/**
 * A trait that implements JsonSerializable for classes that use the Serialize attribute.
 *
 * Usage:
 * <code>
 * class TestClass implements \JsonSerializable
 * {
 *   use PropertyJsonSerializer;
 *
 *   #[Serialize]
 *   protected string $name;
 * }
 * </code>
 *
 * @see Serialize
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
        return (new ArraySerializer())->serialize($this);
    }
}