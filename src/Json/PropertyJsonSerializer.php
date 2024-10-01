<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Exceptions\SerializationException;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\InvalidInputException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use JsonException;

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
     * Deserialize this object from a json string.
     * @param string $json The json string to deserialize.
     * @return static The deserialized object.
     * @throws IncorrectTypeException If the type of the property is incorrect.
     * @throws MissingPropertyException If a required property is missing.
     * @throws UnsupportedTypeException If the type of the property is unsupported.
     * @throws JsonException If the data is invalid json.
     */
    public static function fromJson(string $json): static
    {
        return (new JsonDeserializer(static::class))->deserialize($json);
    }

    /**
     *  Deserialize this object from a json string.
     * @param string $json The json string to deserialize.
     * @return static|null The deserialized object or null if the input is not valid.
     * @throws UnsupportedTypeException If the type of the property is unsupported.
     */
    public static function tryFromJson(string $json): ?static
    {
        try {
            return static::fromJson($json);
        } catch (InvalidInputException|JsonException) {
            return null;
        }
    }

    /**
     * Serialize this object to an associative array for json_encode.
     * @throws MissingPropertyException If a required property is not set.
     * @throws IncorrectTypeException If a non-nullable property is set to null.
     */
    public function jsonSerialize(): array
    {
        return (new ArraySerializer())->serialize($this);
    }
}