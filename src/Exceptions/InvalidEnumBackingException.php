<?php

namespace Aternos\Serializer\Exceptions;

class InvalidEnumBackingException extends InvalidInputException
{
    /**
     * @param class-string $enumType
     * @param string $backingType
     * @param mixed $actualValue
     */
    public function __construct(
        protected string $enumType,
        protected string $backingType,
        protected mixed $actualValue
    )
    {
        $options = [];
        foreach ($enumType::cases() as $case) {
            $options[] = $case->value;
        }

        parent::__construct("Invalid backing value for enum '" . $enumType . "' expected: type '" . $backingType .
            "' (" . implode(", ", $options) . ") found: "
            . var_export($actualValue, true));
    }

    /**
     * @return string
     */
    public function getEnumType(): string
    {
        return $this->enumType;
    }

    /**
     * @return string
     */
    public function getBackingType(): string
    {
        return $this->backingType;
    }

    /**
     * @return mixed
     */
    public function getActualValue(): mixed
    {
        return $this->actualValue;
    }
}
