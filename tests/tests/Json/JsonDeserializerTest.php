<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Json\JsonDeserializer;
use Aternos\Serializer\Test\Src\TestClass;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JsonDeserializerTest extends TestCase
{
    public function testDeserializeJson(): void
    {
        $deserializer = new JsonDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize('{"name":"test","age":18}');
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(18, $testClass->getAge());
        $this->assertSame('test', $testClass->getNotAJsonField());
    }


    public function testDeserializeInvalidData(): void
    {
        $deserializer = new JsonDeserializer(TestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessageIs("Expected '.' to be 'Aternos\Serializer\Test\Src\TestClass' found: 0");
        $deserializer->deserialize("0");
    }

    public function testJsonDeserializerDataIsNotStringOrArray(): void
    {
        $deserializer = new JsonDeserializer(TestClass::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs("Data must be a string or an array.");
        $deserializer->deserialize(0);
    }
}
