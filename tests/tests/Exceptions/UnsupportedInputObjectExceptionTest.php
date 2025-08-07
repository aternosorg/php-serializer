<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\UnsupportedInputObjectException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnsupportedInputObjectException::class)]
class UnsupportedInputObjectExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new UnsupportedInputObjectException("TestClass");
        $this->assertSame("Unsupported input object 'TestClass'", $exception->getMessage());
    }

    public function testConstructWithReason(): void
    {
        $exception = new UnsupportedInputObjectException("TestClass", "test reason");
        $this->assertSame("Unsupported input object 'TestClass': test reason", $exception->getMessage());
    }

    public function testGetType(): void
    {
        $exception = new UnsupportedInputObjectException("TestClass");
        $this->assertSame("TestClass", $exception->getType());
    }
}
