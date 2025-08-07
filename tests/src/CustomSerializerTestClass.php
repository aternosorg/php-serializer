<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use JsonSerializable;

class CustomSerializerTestClass implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize(serializer: new Base64Serializer(), deserializer: new Base64Deserializer(SecondTestClass::class))]
    protected SecondTestClass $testClass;

    #[Serialize(itemSerializer: new Base64Serializer(), itemDeserializer: new Base64Deserializer(SecondTestClass::class))]
    protected array $testArray = [];

    protected int $propertyAfterTestArray = 0;

    public function __construct()
    {
        $this->testClass = new SecondTestClass();
        $this->testArray = [new SecondTestClass(), new SecondTestClass()];
    }

    /**
     * @return SecondTestClass
     */
    public function getTestClass(): SecondTestClass
    {
        return $this->testClass;
    }

    /**
     * @return SecondTestClass[]
     */
    public function getTestArray(): array
    {
        return $this->testArray;
    }

    /**
     * @param array $testArray
     * @return void
     */
    public function setTestArray(array $testArray): void
    {
        $this->testArray = $testArray;
    }
}
