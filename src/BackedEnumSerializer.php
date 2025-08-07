<?php

namespace Aternos\Serializer;

use Aternos\Serializer\Exceptions\UnsupportedInputObjectException;
use BackedEnum;
use UnitEnum;

class BackedEnumSerializer implements SerializerInterface
{
    /**
     * @param BackedEnum|UnitEnum|mixed $item
     * @return string
     * @noinspection PhpUnhandledExceptionInspection,PhpDocMissingThrowsInspection
     */
    public function serialize(object $item): string
    {
        if (!$item instanceof BackedEnum) {
            throw new UnsupportedInputObjectException(get_debug_type($item),
                "Only BackedEnum and UnitEnum are supported by EnumSerializer."
            );
        }
        return $item->value;
    }
}
