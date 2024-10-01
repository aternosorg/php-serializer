<?php

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\Deserializer;
use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationMissingPropertyException;
use Aternos\Serializer\SerializationProperty;
use Aternos\Serializer\SerializationUnsupportedTypeException;
use Aternos\Serializer\Test\Src\IntersectionTestClass;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use Aternos\Serializer\Test\Src\TestClass;
use Aternos\Serializer\Test\Src\UnionIntersectionTestClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Deserializer::class)]
#[UsesClass(SerializationProperty::class)]
#[UsesClass(SerializationIncorrectTypeException::class)]
#[UsesClass(SerializationMissingPropertyException::class)]
#[UsesClass(SerializationUnsupportedTypeException::class)]
class DeserializerTest extends TestCase
{
    public function testDeserialize(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize(["name" => "test", "age" =>18]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(18, $testClass->getAge());
        $this->assertSame('test', $testClass->getNotAJsonField());
    }

    public function testDeserializeInvalidClass(): void
    {
        $deserializer = new Deserializer("non-existant-class");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class 'non-existant-class' does not exist.");
        $deserializer->deserialize([]);
    }

    public function testDeserializeAdditionalProperties(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 18,
            "non-existent-property" => false
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(18, $testClass->getAge());
    }

    public function testDeserializeRenamedProperties(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 18,
            "changedName" => "other-name"
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(18, $testClass->getAge());
        $this->assertSame('other-name', $testClass->getOriginalName());
    }

    public function testDeserializeMissingProperty(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $this->expectException(SerializationMissingPropertyException::class);
        $this->expectExceptionMessage("Missing property '.name' of type 'string'.");
        $deserializer->deserialize(["age" => 18]);
    }

    public function testDeserializeMissingPropertyNotRequired(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize(["name" => "test"]);
        $this->assertSame('test', $testClass->getName());
    }

    public function testDeserializeIncorrectDataType(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $this->expectException(SerializationIncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.age' to be 'int' found: 'eighteen'");
        $deserializer->deserialize([
            "name" => "test",
            "age" => "eighteen"
        ]);
    }

    public function testDeserializeOptionalNotNullable(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "nullable" => 0
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(0, $testClass->getNullable());
    }

    public function testDeserializeOptionalNotNullableNull(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $this->expectException(SerializationIncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.nullable' to be 'int' found: NULL");
        $deserializer->deserialize([
            "name" => "test",
            "nullable" => null
        ]);
    }

    public function testDeserializeUnionTypeBool(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => true
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertTrue($testClass->getBoolOrInt());
    }

    public function testDeserializeUnionTypeInt(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => 1
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(1, $testClass->getBoolOrInt());
    }

    public function testDeserializeUnionTypeString(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $this->expectException(SerializationIncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.boolOrInt' to be 'int|bool' found: 'not-either'");
        $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => "not-either"
        ]);
    }

    public function testDeserializeSecondClass(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "secondTestClass" => ["y" => 123]
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(123, $testClass->getSecondTestClass()?->getY());
    }

    public function testDeserializeMixed(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "mixed" => ["y" => 123]
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(["y" => 123], $testClass->getMixed());
    }

    public function testDeserializeFloat(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "float" => 1.5
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(1.5, $testClass->getFloat());
    }

    public function testDeserializeIntAsFloat(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "float" => 1
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(1.0, $testClass->getFloat());
    }

    public function testDeserializeSecondClassNotArray(): void
    {
        $deserializer = new Deserializer(TestClass::class);
        $this->expectException(SerializationIncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.secondTestClass' to be 'Aternos\Serializer\Test\Src\SecondTestClass' found: 'y'");
        $deserializer->deserialize([
            "name" => "test",
            "secondTestClass" => "y"
        ]);
    }

    public function testDeserializeIntersection(): void
    {
        $deserializer = new Deserializer(IntersectionTestClass::class);
        $this->expectException(SerializationUnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Throwable&Iterator' for property '.x': Intersection types are not supported");
        $deserializer->deserialize([
            "x" => "123"
        ]);
    }

    public function testDeserializeUnionIntersection(): void
    {
        $deserializer = new Deserializer(UnionIntersectionTestClass::class);
        $this->expectException(SerializationUnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Throwable&Iterator' for property '.x': Intersection types are not supported");
        $deserializer->deserialize([
            "x" => "123"
        ]);
    }

    public function testDeserializeArray()
    {
        $deserializer = new Deserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "array" => [1, 2, 3]
        ]);
        $this->assertSame([1, 2, 3], $testClass->getArray());
    }

    public function testDeserializeNullableObjects()
    {
        $deserializer = new Deserializer(SerializerTestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "secondTestClass" => null,
            "testClass" => null
        ]);
        $this->assertNull($testClass->getSecondTestClass());
    }
}