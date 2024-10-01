<?php

namespace Aternos\Serializer;

use Aternos\Serializer\Json\JsonSerializer;
use JsonSerializable;
use ReflectionClass;

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
 * @see JsonSerializer
 */
class Serializer
{
    /**
     * Create a serializer from an object
     * @param object $item the object to serialize
     */
    public function __construct(protected object $item)
    {
    }

    /**
     * Prepare serializing this object with json_encode by converting it to an array.
     * @throws SerializationMissingPropertyException If a required property is not set.
     * @throws SerializationIncorrectTypeException If a non-nullable property is set to null.
     */
    public function serialize(): array
    {
        $reflectionClass = new ReflectionClass($this->item);
        $serializedProperties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $attribute = SerializationProperty::getAttribute($property);
            if (!$attribute) {
                continue;
            }

            if (!$property->isInitialized($this->item)) {
                if ($attribute->isRequired() ?? !$property->hasDefaultValue()) {
                    throw new SerializationMissingPropertyException($property->getName());
                }
                continue;
            }


            $nullable = $attribute->allowsNull() ?? $property->getType()?->allowsNull() ?? true;
            if (!$nullable && $property->getValue($this->item) === null) {
                throw new SerializationIncorrectTypeException($property->getName(), "not null", "null");
            }

            $name = $attribute->getName() ?? $property->getName();
            $value = $property->getValue($this->item);

            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            } elseif (is_object($value)) {
                $value = (new static($value))->serialize();
            }

            $serializedProperties[$name] = $value;
        }

        return $serializedProperties;
    }
}