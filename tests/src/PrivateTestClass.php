<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use JsonSerializable;

class PrivateTestClass implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize]
    protected string $name;

    public static function create(string $name): self
    {
        $instance = new self();
        $instance->name = $name;
        return $instance;
    }

    private function __construct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
