<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\UnsupportedInputObjectException;
use PHPUnit\Framework\TestCase;

class UnsupportedInputObjectExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new UnsupportedInputObjectException("Model");
        $this->assertSame("Unsupported input object 'Model'", $exception->getMessage());
    }

    public function testConstructWithReason(): void
    {
        $exception = new UnsupportedInputObjectException("Model", "test reason");
        $this->assertSame("Unsupported input object 'Model': test reason", $exception->getMessage());
    }

    public function testGetType(): void
    {
        $exception = new UnsupportedInputObjectException("Model");
        $this->assertSame("Model", $exception->getType());
    }
}
