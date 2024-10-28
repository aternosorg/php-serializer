<?php

namespace Aternos\Serializer\Test\Tests\Exceptions;

use Aternos\Serializer\Exceptions\InvalidEnumBackingException;
use Aternos\Serializer\Test\Src\TestBackedEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidEnumBackingException::class)]
class InvalidEnumBackingExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new \Aternos\Serializer\Exceptions\InvalidEnumBackingException(
            TestBackedEnum::class,
            "test2",
            "test3",
        );

        $this->assertSame("Invalid backing value for enum 'Aternos\Serializer\Test\Src\TestBackedEnum' expected: type 'test2' (a, b, c) found: 'test3'", $exception->getMessage());
    }

    public function testGetEnumType(): void
    {
        $exception = new \Aternos\Serializer\Exceptions\InvalidEnumBackingException(
            TestBackedEnum::class,
            "test2",
            "test3",
        );

        $this->assertSame(TestBackedEnum::class, $exception->getEnumType());
    }

    public function testGetBackingType(): void
    {
        $exception = new \Aternos\Serializer\Exceptions\InvalidEnumBackingException(
            TestBackedEnum::class,
            "test2",
            "test3",
        );

        $this->assertSame("test2", $exception->getBackingType());
    }

    public function testGetActualValue(): void
    {
        $exception = new \Aternos\Serializer\Exceptions\InvalidEnumBackingException(
            TestBackedEnum::class,
            "test2",
            "test3",
        );

        $this->assertSame("test3", $exception->getActualValue());
    }
}
