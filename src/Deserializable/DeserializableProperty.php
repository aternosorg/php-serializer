<?php

namespace Aternos\Serializer\Deserializable;

use Aternos\Serializer\Serialize;
use ReflectionProperty;
use ReflectionType;

final class DeserializableProperty implements DeserializableItem
{
    public function __construct(
        protected ReflectionProperty $property,
    )
    {
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    public function getAttribute(): ?Serialize
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection no idea why phpstorm doesn't understand this */
        return $this->property->getAttributes(Serialize::class)[0]?->newInstance() ?? null;
    }

    public function getType(): ?ReflectionType
    {
        return $this->property->getType();
    }

    public function getDefaultValue(): OptionalValue
    {
        if ($this->property->hasDefaultValue()) {
            return OptionalValue::withValue($this->property->getDefaultValue());
        }
        return OptionalValue::withoutValue();
    }
}
