<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class SecondTestClass
{
    #[Serialize]
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