<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\SerializerInterface;

class Base64Serializer implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize(object $item): int|float|string|array|null
    {
        return base64_encode(serialize($item));
    }
}
