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

    public function __construct()
    {
        $this->testClass = new TestClass();
    }

    /**
     * @return TestClass
     */
    public function getTestClass(): TestClass
    {
        return $this->testClass;
    }
}
