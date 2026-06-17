<?php

namespace Aternos\Serializer;

use Aternos\Serializer\Deserializable\DeserializableItem;
use Aternos\Serializer\Deserializable\DeserializableParameter;
use Aternos\Serializer\Deserializable\DeserializableProperty;
use Aternos\Serializer\Deserializable\OptionalValue;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\InvalidEnumBackingException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\SerializationException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionEnum;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use ValueError;

/**
 * Deserializes arrays into objects using the Serialize attribute.
 *
 * Usage:
 * ```php
 * $deserializer = new ArrayDeserializer(TestClass::class);
 * $object = $deserializer->deserialize(["name" => "test", "age" => 18]);
 * ```
 *
 * @see Serialize
 * @template T of object
 */
class ArrayDeserializer implements DeserializerInterface
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
     * @throws InvalidEnumBackingException if the target class is an enum, but the serialized data is not a valid backing value
     */
    public function deserialize(
        mixed  $data,
        string $path = "",
    ): object
    {
        try {
            $reflectionClass = new ReflectionClass($this->class);
        } catch (ReflectionException) {
            throw new InvalidArgumentException("Class '" . $this->class . "' does not exist.");
        }

        if ($reflectionClass->isEnum()) {
            return $this->parseEnum($data, $path);
        }

        if (!is_array($data)) {
            throw new IncorrectTypeException($path, $this->class, $data);
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor) {
            $args = [];
            foreach ($constructor->getParameters() as $parameter) {
                $value = $this->deserializeItem($data, $path, new DeserializableParameter($parameter));
                if ($value->hasValue()) {
                    $args[] = $value->getValue();
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
                } else {
                    throw new UnsupportedTypeException($path, $this->class, "Required parameter '" . $parameter->getName() . "' not annotated as serializable");
                }
            }
            if ($constructor->isPublic()) {
                $result = new $this->class(...$args);
            } else {
                try {
                    $result = $reflectionClass->newInstanceWithoutConstructor();
                    $constructor->invoke($result, $args);
                } catch (ReflectionException $e) {
                    throw new UnsupportedTypeException($path, $this->class, $e->getMessage(), previous: $e);
                }
            }
        } else {
            $result = new $this->class;
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
     * @throws InvalidEnumBackingException if the target class is an enum, but the serialized data is not a valid backing value
     */
    protected function deserializeProperty(
        array              $data,
        string             $path,
        ReflectionProperty $property,
        object             $result
    ): void
    {
        if ($property->isPromoted()) {
            // Promoted properties mean this object was created with a constructor, so these properties are already set
            return;
        }

        $item = $this->deserializeItem(
            $data,
            $path,
            new DeserializableProperty($property)
        );
        if ($item->hasValue()) {
            $value = $item->getValue();
            $property->setValue($result, $value);
        }
    }

    /**
     * Deserialize a single property or parameter of an object
     * @param array $data the data to deserialize
     * @param string $path the path to the data in the base data (used for error messages)
     * @param DeserializableItem $item the property or parameter to deserialize
     * @return OptionalValue the deserialized value. If no value is present, it should not be set on the object.
     * @throws IncorrectTypeException if the type of the property is incorrect
     * @throws MissingPropertyException if the property is required but missing
     * @throws UnsupportedTypeException if the type of the property is unsupported
     * @throws InvalidEnumBackingException if the target class is an enum, but the serialized data is not a valid backing value
     */
    protected function deserializeItem(
        array $data,
        string $path,
        DeserializableItem $item,
    ): OptionalValue
    {
        $attribute = $item->getAttribute();
        if (!$attribute) {
            return OptionalValue::withoutValue();
        }

        $name = $attribute->getName() ?? $item->getName();
        $type = $item->getType();

        if (!array_key_exists($name, $data)) {
            $default = $item->getDefaultValue();
            if ($attribute->isRequired() ?? !$default->hasValue()) {
                throw new MissingPropertyException($path . "." . $name, $type?->getName());
            }

            if ($default->hasValue()) {
                return OptionalValue::withoutValue();
            }

            if (!$type || $type->allowsNull()) {
                return OptionalValue::withValue(null);
            }

            // If there is no default value and the property is not required, we can skip it
            return OptionalValue::withoutValue();
        }

        $value = $data[$name];
        if ($customDeserializer = $attribute->getDeserializer()) {
            $value = $customDeserializer->deserialize($value, $path);
            if ($this->isTypeValid($type, $value, $path)) {
                return OptionalValue::withValue($value);
            }
            throw new IncorrectTypeException($path . "." . $name, $type, $value);
        }

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
                return OptionalValue::withValue(null);
            }
            return OptionalValue::withoutValue();
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

        if (is_array($value) && ($attribute->getItemType() !== null || $attribute->getItemDeserializer() !== null)) {
            $deserializer = $attribute->getItemDeserializer() ?? new static($attribute->getItemType());
            $value = array_map(fn($item) => $deserializer->deserialize($item, $path . "." . $name), $value);
        }

        return OptionalValue::withValue($value);
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
     * @throws InvalidEnumBackingException if the target class is an enum, but the serialized data is not a valid backing value
     */
    protected function parseNamedType(
        ReflectionNamedType $type,
        mixed               $value,
        string              $path,
        string              $name
    ): mixed
    {
        $propertyPath = $path . "." . $name;

        if ($type->isBuiltin()) {
            if (!$this->isBuiltInTypeValid($type->getName(), $value, $propertyPath)) {
                throw new IncorrectTypeException($propertyPath, $type->getName(), $value);
            }

            return $value;
        }

        if ($type->getName() === "self") {
            $deserializer = $this;
        } else {
            $deserializer = new static($type->getName());
        }
        return $deserializer->deserialize($value, $propertyPath);
    }

    /**
     * Check if a built-in type is valid
     * @param string $type the type to check
     * @param mixed $value the value to check
     * @param string $path the path to the data in the base data (used for error messages)
     * @return bool if the type is valid
     * @throws UnsupportedTypeException if the type of the property is unsupported
     */
    protected function isBuiltInTypeValid(string $type, mixed $value, string $path): bool
    {
        return match ($type) {
            "bool" => is_bool($value),
            "int" => is_int($value),
            "float" => is_float($value) || is_int($value),
            "string" => is_string($value),
            "mixed" => true,
            "array" => is_array($value),
            "object" => is_object($value),
            "false" => $value === false,
            "true" => $value === true,
            default => throw new UnsupportedTypeException($path, $type),
        };
    }

    /**
     * Check if a type is valid
     * @param ReflectionType $type
     * @param mixed $value
     * @param string $path
     * @return bool true if the type is valid, false otherwise
     * @throws UnsupportedTypeException if the type of the property is unsupported
     */
    protected function isTypeValid(ReflectionType $type, mixed $value, string $path): bool
    {
        if ($type instanceof ReflectionNamedType) {
            if ($type->isBuiltin()) {
                return $this->isBuiltInTypeValid($type->getName(), $value, $path);
            }
            return $value instanceof ($type->getName());
        } else if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $subType) {
                if ($this->isTypeValid($subType, $value, $path)) {
                    return true;
                }
            }
            return false;
        } else if ($type instanceof ReflectionIntersectionType) {
            foreach ($type->getTypes() as $subType) {
                if (!$this->isTypeValid($subType, $value, $path)) {
                    return false;
                }
            }
            return true;
        } else {
            throw new UnsupportedTypeException($path, $type);
        }
    }

    /**
     * @param mixed $value
     * @param string $path
     * @return mixed
     * @throws InvalidEnumBackingException
     * @throws UnsupportedTypeException
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function parseEnum(mixed $value, string $path): mixed
    {
        /** @noinspection PhpUnhandledExceptionInspection - It has already been verified that the enum exists */
        $reflectionEnum = new ReflectionEnum($this->class);

        if (!$reflectionEnum->isBacked()) {
            throw new UnsupportedTypeException($path, $this->class, "Enums must be backed by a scalar type.");
        }

        $backingType = $reflectionEnum->getBackingType();
        if (!$backingType->isBuiltin() || !$this->isBuiltInTypeValid($backingType->getName(), $value, $path)) {
            throw new InvalidEnumBackingException($this->class, $backingType->getName(), $value);
        }

        try {
            return $this->class::from($value);
        } catch (ValueError) {
            throw new InvalidEnumBackingException($this->class, $backingType->getName(), $value);
        }
    }
}
