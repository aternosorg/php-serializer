<?php

namespace Aternos\Serializer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Class Deserializer
 *
 * Deserializes arrays into objects using the SerializationProperty attribute.
 *
 * Usage:
 * ```php
 * $deserializer = new Deserializer(TestClass::class);
 * $object = $deserializer->deserialize(["name" => "test", "age" => 18]);
 * ```
 *
 * @see SerializationProperty
 * @template T
 */
class Deserializer
{

    /**
     * Create a deserializer for a class
     *
     * @param class-string<T> $class the class to deserialize into
     */
    public function __construct(
        protected string $class,
    )
    {
    }

    /**
     * Deserialize the data into an object
     *
     * @return T
     * @throws SerializationIncorrectTypeException if the type of the property is incorrect
     * @throws SerializationMissingPropertyException if a required property is missing
     * @throws SerializationUnsupportedTypeException if the type of the property is unsupported
     */
    public function deserialize(
        array $data,
        string $path = "",
    ): object
    {
        try {
            $reflectionClass = new ReflectionClass($this->class);
            $result = new $this->class;
        } catch (ReflectionException) {
            throw new InvalidArgumentException("Class '" . $this->class . "' does not exist.");
        }

        foreach ($reflectionClass->getProperties() as $property) {
            $attribute = SerializationProperty::getAttribute($property);
            if (!$attribute) {
                continue;
            }

            $property->setValue($result, $this->parseAttributeValue($data, $path, $property, $attribute));
        }

        return $result;
    }

    /**
     * Parse the value of a property
     *
     * @param array $data the data to parse
     * @param string $path the path to the data in the base input (used for error messages)
     * @param ReflectionProperty $property the property to parse
     * @param SerializationProperty $attribute the attribute of the property
     * @return mixed the attribute parsed value
     * @throws SerializationIncorrectTypeException if the type of the property is incorrect
     * @throws SerializationMissingPropertyException
     * @throws SerializationUnsupportedTypeException if the type of the property is unsupported
     */
    protected function parseAttributeValue(
        array $data,
        string $path,
        ReflectionProperty    $property,
        SerializationProperty $attribute,
    ): mixed
    {
        $name = $attribute->getName() ?? $property->getName();
        $type = $property->getType();

        if (!array_key_exists($name, $data)) {
            if ($attribute->isRequired() || !$property->hasDefaultValue()) {
                throw new SerializationMissingPropertyException($path . "." . $name, $type?->getName());
            }
            return $property->getDefaultValue();
        }

        $value = $data[$name];

        $nullable = $attribute->allowsNull() ?? $type?->allowsNull() ?? true;
        if ($value === null) {
            if (!$nullable) {
                throw new SerializationIncorrectTypeException(
                    $path . "." . $name,
                    $type?->getName() ?? "not null",
                    $value
                );
            }

            return null;
        }

        if ($type instanceof ReflectionNamedType) {
            $value = $this->parseNamedType($type, $value, $path, $name);
        } else if ($type instanceof ReflectionUnionType) {
            $value = $this->parseUnionType($type, $value, $path, $name);
        } else if ($type instanceof ReflectionIntersectionType) {
            throw new SerializationUnsupportedTypeException(
                $path . "." . $name,
                $type,
                'Intersection types are not supported'
            );
        }

        return $value;
    }

    /**
     * Parse a property of a union type
     *
     * @param ReflectionUnionType $unionType the union type to parse
     * @param mixed $value the value to parse
     * @param string $path the path to the data in the base data (used for error messages)
     * @param string $name the name of the property
     * @return mixed the parsed value
     * @throws SerializationIncorrectTypeException if the type of the property is incorrect
     * @throws SerializationUnsupportedTypeException if the type of the property is unsupported
     */
    protected function parseUnionType(ReflectionUnionType $unionType, mixed $value, string $path, string $name): mixed
    {
        $allowedTypes = [];

        foreach ($unionType->getTypes() as $type) {
            if ($type instanceof ReflectionNamedType) {
                $allowedTypes[] = $type->getName();
                try {
                    return $this->parseNamedType($type, $value, $path, $name);
                } catch (SerializationException) {
                    continue;
                }
            } else if ($type instanceof ReflectionIntersectionType) {
                throw new SerializationUnsupportedTypeException(
                    $path . "." . $name,
                    $type,
                    'Intersection types are not supported'
                );
            }
        }

        throw new SerializationIncorrectTypeException($path . "." . $name, implode('|', $allowedTypes), $value);
    }

    /**
     * Parse a property of a named type
     *
     * @param ReflectionNamedType $type the named type to parse
     * @param mixed $value the value to parse
     * @param string $path the path to the data in the base data (used for error messages)
     * @param string $name the name of the property
     * @return mixed the parsed value
     * @throws SerializationIncorrectTypeException if the type of the property is incorrect
     * @throws SerializationUnsupportedTypeException if the type of the property is unsupported
     * @throws SerializationMissingPropertyException
     */
    protected function  parseNamedType(ReflectionNamedType $type, mixed $value, string $path, string $name): mixed
    {
        if ($type->isBuiltin()) {
            $valid = match ($type->getName()) {
                "int" => is_int($value),
                "float" => is_float($value) || is_int($value),
                "string" => is_string($value),
                "bool" => is_bool($value),
                "mixed" => true,
                "array" => is_array($value),
                default => throw new SerializationUnsupportedTypeException(
                    $path . "." . $name,
                    $type->getName(),
                ),
            };

            if (!$valid) {
                throw new SerializationIncorrectTypeException($path . "." . $name, $type->getName(), $value);
            }
            return $value;
        }

        if (!is_array($value)) {
            throw new SerializationIncorrectTypeException($path . "." . $name, $type->getName(), $value);
        }

        $deserializer = new static($type->getName());
        return $deserializer->deserialize($value, $path . "." . $name);
    }
}