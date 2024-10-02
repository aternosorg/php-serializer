<?php

namespace Aternos\Serializer;

use Aternos\Serializer\Exceptions\SerializationException;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Deserializes arrays into objects using the Serialize attribute.
 *
 * Usage:
 * ```php
 * $deserializer = new Deserializer(TestClass::class);
 * $object = $deserializer->deserialize(["name" => "test", "age" => 18]);
 * ```
 *
 * @see Serialize
 * @template T
 */
class ArrayDeserializer
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
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws MissingPropertyException if a required property is missing
     * @throws UnsupportedTypeException if the type of the property is unsupported
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
            $this->deserializeProperty($data, $path, $property, $result);
        }

        return $result;
    }

    /**
     * Deserialize a property of an object
     * @param array $data the data to deserialize
     * @param string $path the path to the data in the base data (used for error messages)
     * @param ReflectionProperty $property the property to deserialize
     * @param object $result the object to deserialize into
     * @return void
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws MissingPropertyException if the property is required but missing
     * @throws UnsupportedTypeException if the type of the property is unsupported
     */
    protected function deserializeProperty(
        array $data,
        string $path,
        ReflectionProperty $property,
        object $result
    )
    {
        $attribute = Serialize::getAttribute($property);
        if (!$attribute) {
            return;
        }

        $name = $attribute->getName() ?? $property->getName();
        $type = $property->getType();

        if (!array_key_exists($name, $data)) {
            if ($attribute->isRequired() ?? !$property->hasDefaultValue()) {
                throw new MissingPropertyException($path . "." . $name, $type?->getName());
            }

            if ($property->hasDefaultValue()) {
                $property->setValue($result, $property->getDefaultValue());
                return;
            }

            if (!$type || $type->allowsNull()) {
                $property->setValue($result, null);
            }

            // If there is no default value and the property is not required, we can skip it
            return;
        }

        $value = $data[$name];

        $nullable = $attribute->allowsNull() ?? $type?->allowsNull() ?? true;
        if ($value === null) {
            if (!$nullable) {
                throw new IncorrectTypeException(
                    $path . "." . $name,
                    $type?->getName() ?? "not null",
                    $value
                );
            }

            if (!$type || $type->allowsNull()) {
                $property->setValue($result, null);
            }
            return;
        }

        if ($type instanceof ReflectionNamedType) {
            $value = $this->parseNamedType($type, $value, $path, $name);
        } else if ($type instanceof ReflectionUnionType) {
            $value = $this->parseUnionType($type, $value, $path, $name);
        } else if ($type instanceof ReflectionIntersectionType) {
            throw new UnsupportedTypeException(
                $path . "." . $name,
                $type,
                'Intersection types are not supported'
            );
        }

        if (is_array($value) && $attribute->itemType() !== null) {
            $deserializer = new static($attribute->itemType());
            $value = array_map(fn($item) => $deserializer->deserialize($item, $path . "." . $name), $value);
        }

        $property->setValue($result, $value);
    }

    /**
     * Parse a property of a union type
     *
     * @param ReflectionUnionType $unionType the union type to parse
     * @param mixed $value the value to parse
     * @param string $path the path to the data in the base data (used for error messages)
     * @param string $name the name of the property
     * @return mixed the parsed value
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws UnsupportedTypeException if the type of the property is unsupported
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
                throw new UnsupportedTypeException(
                    $path . "." . $name,
                    $type,
                    'Intersection types are not supported'
                );
            }
        }

        throw new IncorrectTypeException($path . "." . $name, implode('|', $allowedTypes), $value);
    }

    /**
     * Parse a property of a named type
     *
     * @param ReflectionNamedType $type the named type to parse
     * @param mixed $value the value to parse
     * @param string $path the path to the data in the base data (used for error messages)
     * @param string $name the name of the property
     * @return mixed the parsed value
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws UnsupportedTypeException if the type of the property is unsupported
     * @throws MissingPropertyException if a required property is missing
     */
    protected function  parseNamedType(
        ReflectionNamedType $type,
        mixed               $value,
        string              $path,
        string              $name
    ): mixed
    {
        if ($type->isBuiltin()) {
            $valid = match ($type->getName()) {
                "bool" => is_bool($value),
                "int" => is_int($value),
                "float" => is_float($value) || is_int($value),
                "string" => is_string($value),
                "mixed" => true,
                "array" => is_array($value),
                "object" => is_object($value),
                "false" => $value === false,
                "true" => $value === true,
                default => throw new UnsupportedTypeException($path . "." . $name, $type->getName()),
            };

            if (!$valid) {
                throw new IncorrectTypeException($path . "." . $name, $type->getName(), $value);
            }

            return $value;
        }

        if (!is_array($value)) {
            throw new IncorrectTypeException($path . "." . $name, $type->getName(), $value);
        }

        if ($type->getName() === "self") {
            $deserializer = $this;
        } else {
            $deserializer = new static($type->getName());
        }
        return $deserializer->deserialize($value, $path . "." . $name);
    }
}