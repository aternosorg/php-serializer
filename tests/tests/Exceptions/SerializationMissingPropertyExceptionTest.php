<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\MissingPropertyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MissingPropertyException::class)]
class SerializationMissingPropertyExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new MissingPropertyException(".value");

        $this->assertSame("Missing property '.value'.", $exception->getMessage());
    }

    public function testConstructWithType(): void
    {
        $exception = new MissingPropertyException(
            ".value",
            "int",
        );

        $this->assertSame("Missing property '.value' of type 'int'.", $exception->getMessage());
    }

    public function testGetPropertyPath(): void
    {
        $exception = new MissingPropertyException(
            ".value",
            "int",
        );

        $this->assertSame(".value", $exception->getPropertyPath());
    }

    public function testGetExpectedType(): void
    {
        $exception = new MissingPropertyException(
            ".value",
            "int",
        );

        $this->assertSame("int", $exception->getExpectedType());
    }
}
