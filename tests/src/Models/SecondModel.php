<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;

class SecondModel
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
