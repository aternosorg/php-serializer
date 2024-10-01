<?php

namespace Aternos\Serializer;

use Attribute;
use ReflectionProperty;

/**
 * Attribute Serialize
 *
 * Used to mark a property for (de-)serialization.
 *
 * You can specify the following options:
 * - name: the name of the field in the serialized data (defaults to the property name)
 * - required: whether the field is required (defaults to false if the property has a default value)
 * - allowNull: whether the field can be null (defaults to true unless the property has a non-nullable type)
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Serialize
{
    public static function getAttribute(ReflectionProperty $property): ?self
    {
        $attribute = $property->getAttributes(self::class);
        if (count($attribute) === 0) {
            return null;
        }

        return $attribute[0]->newInstance();
    }

    public function __construct(
        protected ?string $name = null,
        protected ?bool   $required = null,
        protected ?bool   $allowNull = null,
    )
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function allowsNull(): ?bool
    {
        return $this->allowNull;
    }
}