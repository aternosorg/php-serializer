<?php

namespace Aternos\Serializer\Exceptions;

/**
 * An exception that is thrown when an unsupported type is encountered during (de)-serialization.
 */
class UnsupportedTypeException extends SerializationException
{
    /**
     * @param string $type the name of the unsupported type
     */
    public function __construct(
        protected string  $propertyPath,
        protected string  $type,
        protected ?string $reason = null,
    )
    {
        $message = "Unsupported type '" . $type . "' for property '" . $propertyPath . "'";
        if ($reason !== null) {
            $message .= ": " . $reason;
        }
        parent::__construct($message);
    }

    /**
     * Get the path to the property with the unsupported type.
     * @return string
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /**
     * Get the name of the unsupported type.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the reason why the type is unsupported.
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}