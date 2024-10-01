<?php

namespace Aternos\Serializer\Exceptions;

/**
 * Exception SerializationIncorrectTypeException
 *
 * An exception that is thrown when a property has an incorrect type.
 *
 */
class SerializationIncorrectTypeException extends SerializationException
{

    /**
     * @param string $propertyPath path to the property with the incorrect type
     * @param string $expectedType expected property type
     * @param mixed $actualValue actual value of property
     */
    public function __construct(
        protected string     $propertyPath,
        protected string     $expectedType,
        protected mixed      $actualValue,
    )
    {
        parent::__construct("Expected '" . $propertyPath . "' to be '" . $expectedType . "' found: "
            . var_export($actualValue, true));
    }

    /**
     * Get the path to the property with the incorrect type.
     * @return string
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /**
     * Get the expected property type.
     * @return string
     */
    public function getExpectedType(): string
    {
        return $this->expectedType;
    }

    /**
     * Get the actual value of the property.
     * @return mixed
     */
    public function getActualValue(): mixed
    {
        return $this->actualValue;
    }
}