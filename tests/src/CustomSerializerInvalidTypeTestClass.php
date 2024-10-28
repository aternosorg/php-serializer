<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use JsonSerializable;

class CustomSerializerInvalidTypeTestClass implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize(serializer: new Base64Serializer(), deserializer: new Base64Deserializer(SecondTestClass::class))]
    protected TestClass $testClass;

    #[Serialize(itemSerializer: new Base64Serializer(), itemDeserializer: new Base64Deserializer(SecondTestClass::class))]
    protected array $testArray = [];

    public function __construct()
    {
        $this->testClass = new TestClass();
    }
}
