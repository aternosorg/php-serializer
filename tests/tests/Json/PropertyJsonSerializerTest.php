<?php

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationMissingPropertyException;
use Aternos\Serializer\SerializationProperty;
use Aternos\Serializer\Serializer;
use Aternos\Serializer\Test\Src\SecondTestClass;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PropertyJsonSerializer::class)]
#[UsesClass(Serializer::class)]
#[UsesClass(SerializationProperty::class)]
#[UsesClass(SerializationIncorrectTypeException::class)]
#[UsesClass(SerializationMissingPropertyException::class)]
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
        $this->expectException(SerializationMissingPropertyException::class);
        json_encode($testClass);
    }

    public function testSerializeNotNull(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $testClass->setNotNullable(null);
        $this->expectException(SerializationIncorrectTypeException::class);
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
}