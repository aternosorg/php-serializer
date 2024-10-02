<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use JsonSerializable;

class SerializerTestClass implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize]
    protected string $name;

    #[Serialize(required: true)]
    protected int $age = 0;

    /** @noinspection PhpUnused */
    protected string $notAJsonField = "test";

    #[Serialize(allowNull: false)]
    protected ?string $notNullable = "asd";

    #[Serialize(required: false, allowNull: true)]
    public string $nullable;

    #[Serialize(required: false)]
    protected ?SecondTestClass $secondTestClass;

    #[Serialize(required: false)]
    protected ?TestClass $testClass;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getNotNullable(): ?string
    {
        return $this->notNullable;
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

    public function setTestClass(?TestClass $testClass): void
    {
        $this->testClass = $testClass;
    }
}