<?php

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Json\JsonDeserializer;
use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationProperty;
use Aternos\Serializer\Test\Src\TestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonDeserializer::class)]
#[UsesClass(SerializationProperty::class)]
#[UsesClass(SerializationIncorrectTypeException::class)]
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
        $this->expectException(SerializationIncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.' to be 'Aternos\Serializer\Test\Src\TestClass' found: 0");
        $deserializer->deserialize("0");
    }
}
