<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class PrivateConstructorParamTestClass
{
    private function __construct(
        #[Serialize]
        private string $name,
        #[Serialize]
        private int $age,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}
