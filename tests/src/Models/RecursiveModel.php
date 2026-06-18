<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;

class RecursiveModel
{
    #[Serialize]
    protected ?self $next = null;
    #[Serialize]
    protected int $x;

    public function getNext(): ?RecursiveModel
    {
        return $this->next;
    }

    public function getX(): int
    {
        return $this->x;
    }
}
