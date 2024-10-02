<?php

namespace Aternos\Serializer;

use Attribute;
use ReflectionProperty;

/**
 * Used to mark a property for (de-)serialization.
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

    /**
     * @param string|null $name The name of the field in the serialized data
     * @param bool|null $required Whether the field is required
     * @param bool|null $allowNull Whether the field can be null
     * @param class-string|null $itemType The type of the items in the array
     */
    public function __construct(
        protected ?string $name = null,
        protected ?bool   $required = null,
        protected ?bool   $allowNull = null,
        protected ?string $itemType = null,
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

    /**
     * @return class-string|null
     */
    public function getItemType(): ?string
    {
        return $this->itemType;
    }
}