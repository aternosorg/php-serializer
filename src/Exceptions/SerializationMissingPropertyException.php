<?php

namespace Aternos\Serializer\Exceptions;

/**
 * Exception SerializationMissingPropertyException
 *
 * An exception that is thrown when a required property is missing.
 *
 * @package Aternos\Serializer
 */

class SerializationMissingPropertyException extends SerializationException
{
    /**
     * @param string $propertyPath path to the missing property
     * @param string|null $expectedType expected property type
     */
    public function __construct(
        protected string  $propertyPath,
        protected ?string $expectedType = null,
    )
    {
        $message = "Missing property '" . $propertyPath . "'";
        if ($expectedType !== null) {
            $message .= " of type '" . $expectedType . "'";
        }

        parent::__construct($message . ".");
    }

    /**
     * Get the path to the missing property.
     * @return string
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /**
     * Get the expected property type.
     * @return string|null
     */
    public function getExpectedType(): ?string
    {
        return $this->expectedType;
    }
}