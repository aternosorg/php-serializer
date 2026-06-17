<?php

namespace Aternos\Serializer\Deserializable;

/**
 * @internal
 * @template T
 */
final class OptionalValue
{
    /**
     * @param T $value
     * @return self<T>
     */
    public static function withValue(mixed $value): self
    {
        return new self(true, $value);
    }

    public static function withoutValue(): self
    {
        return new self(false, null);
    }

    /**
     * @param bool $hasValue
     * @param T $value
     */
    private function __construct(
        protected bool $hasValue,
        protected mixed $value,
    )
    {

    }

    public function hasValue(): bool
    {
        return $this->hasValue;
    }

    /**
     * @return T
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
