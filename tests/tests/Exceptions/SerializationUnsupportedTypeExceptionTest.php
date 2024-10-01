<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnsupportedTypeException::class)]
class SerializationUnsupportedTypeExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new UnsupportedTypeException(".x", "int");

        $this->assertSame("Unsupported type 'int' for property '.x'", $exception->getMessage());
    }

    public function testConstructWithReason(): void
    {
        $exception = new UnsupportedTypeException(".x", "int", "test");

        $this->assertSame("Unsupported type 'int' for property '.x': test", $exception->getMessage());
    }

    public function testGetPropertyPath(): void
    {
        $exception = new UnsupportedTypeException(".x", "int");

        $this->assertSame(".x", $exception->getPropertyPath());
    }

    public function testGetType(): void
    {
        $exception = new UnsupportedTypeException(".x", "int");

        $this->assertSame("int", $exception->getType());
    }

    public function testGetReason(): void
    {
        $exception = new UnsupportedTypeException(".x", "int", "test");

        $this->assertSame("test", $exception->getReason());
    }
}
