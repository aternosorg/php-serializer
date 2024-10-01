<?php

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\SerializationMissingPropertyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SerializationMissingPropertyException::class)]
class SerializationMissingPropertyExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new SerializationMissingPropertyException(".value");

        $this->assertSame("Missing property '.value'.", $exception->getMessage());
    }

    public function testConstructWithType(): void
    {
        $exception = new SerializationMissingPropertyException(
            ".value",
            "int",
        );

        $this->assertSame("Missing property '.value' of type 'int'.", $exception->getMessage());
    }

    public function testGetPropertyPath(): void
    {
        $exception = new SerializationMissingPropertyException(
            ".value",
            "int",
        );

        $this->assertSame(".value", $exception->getPropertyPath());
    }

    public function testGetExpectedType(): void
    {
        $exception = new SerializationMissingPropertyException(
            ".value",
            "int",
        );

        $this->assertSame("int", $exception->getExpectedType());
    }
}
