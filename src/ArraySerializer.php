<?php

namespace Aternos\Serializer;

use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Json\JsonSerializer;
use JsonSerializable;
use ReflectionClass;

/**
 * A class that serializes objects into arrays using the Serialize attribute.
 *
 * Usage:
 * <code>
 * $serializer = new ArraySerializer();
 * $data = $serializer->serialize(new TestClass());
 * $json = json_encode($data);
 * </code>
 *
 * If a property is not initialized and is either marked as required or doesn't have a default value,
 * a SerializationMissingPropertyException will be thrown.
 * If a property is null and marked as not nullable, a SerializationIncorrectTypeException will be thrown.
 *
 * @see Serialize
 * @see JsonSerializer
 */
class ArraySerializer implements SerializerInterface
{
    /**
     * Create a serializer from an object
     */
    public function __construct()
    {
    }

    /**
     * Prepare serializing this object with json_encode by converting it to an array.
     * @param object $item the object to serialize
     * @return array the serialized object
     * @throws MissingPropertyException If a required property is not set.
     * @throws IncorrectTypeException If a non-nullable property is set to null.
     */
    public function serialize(object $item): array
    {
        $reflectionClass = new ReflectionClass($item);
        $serializedProperties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $attribute = Serialize::getAttribute($property);
            if (!$attribute) {
                continue;
            }

            if (!$property->isInitialized($item)) {
                if ($attribute->isRequired() ?? !$property->hasDefaultValue()) {
                    throw new MissingPropertyException($property->getName());
                }
                continue;
            }


            $nullable = $attribute->allowsNull() ?? $property->getType()?->allowsNull() ?? true;
            if (!$nullable && $property->getValue($item) === null) {
                throw new IncorrectTypeException($property->getName(), "not null", "null");
            }

            $name = $attribute->getName() ?? $property->getName();
            $value = $property->getValue($item);

            if ($customSerializer = $attribute->getSerializer()) {
                $value = $customSerializer->serialize($value);
            } else if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            } elseif (is_object($value)) {
                $value = $this->serialize($value);
            }

            $serializedProperties[$name] = $value;
        }

        return $serializedProperties;
    }
}
