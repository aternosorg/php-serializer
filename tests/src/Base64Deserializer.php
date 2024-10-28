<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\DeserializerInterface;

class Base64Deserializer implements DeserializerInterface
{

    /**
     * @inheritDoc
     */
    public function __construct(protected string $class)
    {
    }

    /**
     * @inheritDoc
     */
    public function deserialize(mixed $data, string $path = ""): object
    {
        return unserialize(base64_decode($data));
    }
}
