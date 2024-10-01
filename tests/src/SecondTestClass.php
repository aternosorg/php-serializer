<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\SerializationProperty;

class SecondTestClass
{
    #[SerializationProperty]
    protected int $y;

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y): void
    {
        $this->y = $y;
    }
}