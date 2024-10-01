<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\SerializationProperty;

class TestClass implements \JsonSerializable
{
    use PropertyJsonSerializer;

    #[SerializationProperty]
    protected string $name;

    #[SerializationProperty(required: false)]
    protected int $age = 0;

    #[SerializationProperty(name: "changedName")]
    protected ?string $originalName = null;

    #[SerializationProperty(required: false, allowNull: false)]
    protected ?int $nullable = null;

    #[SerializationProperty]
    protected bool|int $boolOrInt = false;

    protected string $notAJsonField = "test";

    #[SerializationProperty]
    protected ?SecondTestClass $secondTestClass = null;

    #[SerializationProperty]
    protected mixed $mixed = null;

    #[SerializationProperty]
    protected ?float $float = null;

    #[SerializationProperty(required: false)]
    protected ?array $array = null;

    public function __construct()
    {
    }

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

    public function getNullable(): ?int
    {
        return $this->nullable;
    }

    public function setNullable(?int $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function getBoolOrInt(): bool|int
    {
        return $this->boolOrInt;
    }

    public function getNotAJsonField(): string
    {
        return $this->notAJsonField;
    }

    public function getSecondTestClass(): ?SecondTestClass
    {
        return $this->secondTestClass;
    }

    public function getMixed(): mixed
    {
        return $this->mixed;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function getFloat(): ?float
    {
        return $this->float;
    }

    public function getArray(): ?array
    {
        return $this->array;
    }
}