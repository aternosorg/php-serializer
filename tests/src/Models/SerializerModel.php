<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use JsonSerializable;

class SerializerModel implements JsonSerializable
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
    protected ?SecondModel $secondModel;

    #[Serialize(required: false)]
    protected ?FirstModel $model;

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

    public function getSecondModel(): ?SecondModel
    {
        return $this->secondModel;
    }

    public function setSecondModel(?SecondModel $secondModel): void
    {
        $this->secondModel = $secondModel;
    }

    public function setModel(?FirstModel $model): void
    {
        $this->model = $model;
    }
}
