<?php

namespace Aternos\Serializer\Test\Src;

use Exception;
use Iterator;

class ThrowableIterator extends Exception implements Iterator
{
    private int $position = 0;

    public function current(): mixed { return null; }
    public function key(): mixed { return $this->position; }
    public function next(): void { $this->position++; }
    public function rewind(): void { $this->position = 0; }
    public function valid(): bool { return false; }
}
