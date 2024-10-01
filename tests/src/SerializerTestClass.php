<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\SerializationProperty;

class SerializerTestClass implements \JsonSerializable
{
    use PropertyJsonSerializer;

    #[SerializationProperty]
    protected string $name;

    #[SerializationProperty(required: true)]
    protected int $age = 0;

    protected string $notAJsonField = "test";

    #[SerializationProperty(allowNull: false)]
    protected ?string $notNullable = "asd";

    #[SerializationProperty(required: false)]
    protected ?SecondTestClass $secondTestClass;

    #[SerializationProperty(required: false)]
    protected ?TestClass $testClass;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function setNotNullable(?string $notNullable): void
    {
        $this->notNullable = $notNullable;
    }

    public function getSecondTestClass(): ?SecondTestClass
    {
        return $this->secondTestClass;
    }

    public function setSecondTestClass(?SecondTestClass $secondTestClass): void
    {
        $this->secondTestClass = $secondTestClass;
    }

    public function getTestClass(): ?TestClass
    {
        return $this->testClass;
    }

    public function setTestClass(?TestClass $testClass): void
    {
        $this->testClass = $testClass;
    }
}