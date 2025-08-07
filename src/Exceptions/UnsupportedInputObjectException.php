<?php

namespace Aternos\Serializer\Exceptions;

/**
 * An exception that is thrown when the object used as input to a serializer is not supported by the serializer.
 */
class UnsupportedInputObjectException extends SerializationException
{
    public function __construct(
        protected string $type,
        string $reason = null,
    )
    {
        $message = "Unsupported input object '" . $type . "'";
        if ($reason !== null) {
            $message .= ": " . $reason;
        }
        parent::__construct($message);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
