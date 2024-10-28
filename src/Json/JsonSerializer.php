<?php

namespace Aternos\Serializer\Json;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\SerializerInterface;

/**
 * A class that serializes objects using the Serialize attribute.
 *
 * Usage:
 * <code>
 * $serializer = new JsonSerializer();
 * $json = $serializer->serialize(new TestClass());
 * </code>
 *
 * @see ArraySerializer
 * @see PropertyJsonSerializer
 */
class JsonSerializer implements SerializerInterface
{
    protected ArraySerializer $arraySerializer;

    public function __construct()
    {
        $this->arraySerializer = new ArraySerializer();
    }

    /**
     * Serialize this object to a JSON string.
     * @param object $item the object to serialize
     * @return string the serialized object
     * @throws MissingPropertyException If a required property is not set.
     * @throws IncorrectTypeException If a non-nullable property is set to null.
     */
    public function serialize(object $item): string
    {
        return json_encode($this->arraySerializer->serialize($item));
    }
}
