<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\SerializationUnsupportedTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SerializationUnsupportedTypeException::class)]
class SerializationUnsupportedTypeExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new SerializationUnsupportedTypeException(".x", "int");

        $this->assertSame("Unsupported type 'int' for property '.x'", $exception->getMessage());
    }

    public function testConstructWithReason(): void
    {
        $exception = new SerializationUnsupportedTypeException(".x", "int", "test");

        $this->assertSame("Unsupported type 'int' for property '.x': test", $exception->getMessage());
    }

    public function testGetPropertyPath(): void
    {
        $exception = new SerializationUnsupportedTypeException(".x", "int");

        $this->assertSame(".x", $exception->getPropertyPath());
    }

    public function testGetType(): void
    {
        $exception = new SerializationUnsupportedTypeException(".x", "int");

        $this->assertSame("int", $exception->getType());
    }

    public function testGetReason(): void
    {
        $exception = new SerializationUnsupportedTypeException(".x", "int", "test");

        $this->assertSame("test", $exception->getReason());
    }
}
