<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use JsonSerializable;

class TestClass implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize]
    protected string $name;

    #[Serialize(required: false)]
    protected int $age = 0;

    #[Serialize(name: "changedName")]
    protected ?string $originalName = null;

    #[Serialize(required: false, allowNull: false)]
    protected ?int $nullable = null;

    #[Serialize]
    protected bool|int $boolOrInt = false;

    protected string $notAJsonField = "test";

    #[Serialize]
    protected ?SecondTestClass $secondTestClass = null;

    #[Serialize]
    protected mixed $mixed = null;

    #[Serialize]
    protected ?float $float = null;

    #[Serialize(required: false)]
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