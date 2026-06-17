<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class RecursiveTestClass
{
    #[Serialize]
    protected ?self $next = null;
    #[Serialize]
    protected int $x;

    public function getNext(): ?RecursiveTestClass
    {
        return $this->next;
    }

    public function getX(): int
    {
        return $this->x;
    }
}
