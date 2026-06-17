<?php

namespace Aternos\Serializer\Deserializable;

use Aternos\Serializer\Serialize;
use ReflectionParameter;
use ReflectionType;

final class DeserializableParameter implements DeserializableItem
{
    public function __construct(
        protected ReflectionParameter $parameter,
    )
    {
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function getAttribute(): ?Serialize
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection no idea why phpstorm doesn't understand this */
        return $this->parameter->getAttributes(Serialize::class)[0]?->newInstance() ?? null;
    }

    public function getType(): ?ReflectionType
    {
        return $this->parameter->getType();
    }

    public function getDefaultValue(): OptionalValue
    {
        if ($this->parameter->isDefaultValueAvailable()) {
            /** @noinspection PhpUnhandledExceptionInspection this isn't thrown in the condition above */
            return OptionalValue::withValue($this->parameter->getDefaultValue());
        }
        return OptionalValue::withoutValue();
    }
}
