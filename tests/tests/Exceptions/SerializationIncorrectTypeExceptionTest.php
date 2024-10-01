<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\IncorrectTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IncorrectTypeException::class)]
class SerializationIncorrectTypeExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new IncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame("Expected '.value' to be 'int' found: 'test'", $exception->getMessage());
    }

    public function testGetPropertyPath(): void
    {
        $exception = new IncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame(".value", $exception->getPropertyPath());
    }

    public function testGetExpectedType(): void
    {
        $exception = new IncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame("int", $exception->getExpectedType());
    }

    public function testGetActualValue(): void
    {
        $exception = new IncorrectTypeException(
            ".value",
            "int",
            "test",
        );

        $this->assertSame("test", $exception->getActualValue());
    }
}
