<?php

namespace Aternos\Serializer\Deserializable;

use Aternos\Serializer\Serialize;
use ReflectionType;

/**
 * A constructor parameter or property.
 */
interface DeserializableItem
{
    /**
     * Get the name of the parameter or property
     * @return string
     */
    public function getName(): string;

    /**
     * Get the first Serialize attribute if one exists
     * @return Serialize|null
     */
    public function getAttribute(): ?Serialize;

    /**
     * Get the type of the parameter or property
     * @return ReflectionType|null
     */
    public function getType(): ?ReflectionType;

    /**
     * Get the default value of the parameter or property
     * @return OptionalValue
     */
    public function getDefaultValue(): OptionalValue;
}
