<?php

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\SerializationIncorrectTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SerializationIncorrectTypeException::class)]
class SerializationIncorrectTypeExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new SerializationIncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame("Expected '.value' to be 'int' found: 'test'", $exception->getMessage());
    }

    public function testGetPropertyPath(): void
    {
        $exception = new SerializationIncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame(".value", $exception->getPropertyPath());
    }

    public function testGetExpectedType(): void
    {
        $exception = new SerializationIncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame("int", $exception->getExpectedType());
    }

    public function testGetActualValue(): void
    {
        $exception = new SerializationIncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame("test", $exception->getActualValue());
    }
}
