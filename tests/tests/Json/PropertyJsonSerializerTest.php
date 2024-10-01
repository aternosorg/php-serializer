<?php

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\ArrayDeserializer;
use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Json\JsonDeserializer;
use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\SecondTestClass;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PropertyJsonSerializer::class)]
#[UsesClass(ArraySerializer::class)]
#[UsesClass(ArrayDeserializer::class)]
#[UsesClass(JsonDeserializer::class)]
#[UsesClass(Serialize::class)]
#[UsesClass(IncorrectTypeException::class)]
#[UsesClass(MissingPropertyException::class)]
class PropertyJsonSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $this->assertSame('{"name":"test","age":0,"notNullable":"asd"}', json_encode($testClass));
    }

    public function testSerializeNoName(): void
    {
        $testClass = new SerializerTestClass();
        $this->expectException(MissingPropertyException::class);
        json_encode($testClass);
    }

    public function testSerializeNotNull(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $testClass->setNotNullable(null);
        $this->expectException(IncorrectTypeException::class);
        json_encode($testClass);
    }

    public function testSerializeOtherClass(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $secondClass = new SecondTestClass();
        $secondClass->setY(1);
        $testClass->setSecondTestClass($secondClass);
        $this->assertSame('{"name":"test","age":0,"notNullable":"asd","secondTestClass":{"y":1}}', json_encode($testClass));
    }

    public function testFromJson(): void
    {
        $testClass = SerializerTestClass::fromJson('{"name":"test","age":0,"notNullable":"asd"}');
        $this->assertSame("test", $testClass->getName());
        $this->assertSame(0, $testClass->getAge());
        $this->assertSame("asd", $testClass->getNotNullable());
    }

    public function testTryFromJson(): void
    {
        $testClass = SerializerTestClass::tryFromJson('{"name":"test","age":0,"notNullable":"asd"}');
        $this->assertNotNull($testClass);
        $this->assertSame("test", $testClass->getName());
        $this->assertSame(0, $testClass->getAge());
        $this->assertSame("asd", $testClass->getNotNullable());
    }

    public function testFromJsonInvalidJson(): void
    {
        $this->expectException(JsonException::class);
        SerializerTestClass::fromJson('{');
    }

    public function testTryFromJsonInvalidJson(): void
    {
        $this->assertNull(SerializerTestClass::tryFromJson('{'));
    }

    public function testFromJsonMissingProperty(): void
    {
        $this->expectException(MissingPropertyException::class);
        SerializerTestClass::fromJson('{}');
    }

    public function testTryFromJsonMissingProperty(): void
    {
        $this->assertNull(SerializerTestClass::tryFromJson('{}'));
    }

    public function testFromJsonIncorrectType(): void
    {
        $this->expectException(IncorrectTypeException::class);
        SerializerTestClass::fromJson('{"name":1}');
    }

    public function testTryFromJsonIncorrectType(): void
    {
        $this->assertNull(SerializerTestClass::tryFromJson('{"name":1}'));
    }
}