<?php

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\SerializationIncorrectTypeException;
use Aternos\Serializer\SerializationMissingPropertyException;
use Aternos\Serializer\SerializationProperty;
use Aternos\Serializer\Serializer;
use Aternos\Serializer\Test\Src\SecondTestClass;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use Aternos\Serializer\Test\Src\TestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Serializer::class)]
#[UsesClass(PropertyJsonSerializer::class)]
#[UsesClass(SerializationProperty::class)]
#[UsesClass(SerializationIncorrectTypeException::class)]
#[UsesClass(SerializationMissingPropertyException::class)]
class SerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $serializer = new Serializer();
        $this->assertSame([
            "name" => "test",
            "age" => 0,
            "notNullable" => "asd",
        ], $serializer->serialize($testClass));
    }

    public function testSerializeNoName(): void
    {
        $testClass = new SerializerTestClass();
        $serializer = new Serializer();
        $this->expectException(SerializationMissingPropertyException::class);
        $serializer->serialize($testClass);
    }

    public function testSerializeNotNull(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $testClass->setNotNullable(null);
        $serializer = new Serializer();
        $this->expectException(SerializationIncorrectTypeException::class);
        $serializer->serialize($testClass);
    }

    public function testSerializeOtherClass(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $secondClass = new SecondTestClass();
        $secondClass->setY(1);
        $testClass->setSecondTestClass($secondClass);
        $serializer = new Serializer();
        $this->assertSame([
            'name' => 'test',
            'age' => 0,
            'notNullable' => 'asd',
            'secondTestClass' => [
                'y' => 1,
            ],
        ], $serializer->serialize($testClass));
    }

    public function testSerializeOtherClassJsonSerializable(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $otherTestClass = new TestClass();
        $otherTestClass->setName('test');
        $otherTestClass->setNullable(1);
        $testClass->setTestClass($otherTestClass);
        $serializer = new Serializer();
        $this->assertSame([
            'name' => 'test',
            'age' => 0,
            'notNullable' => 'asd',
            'testClass' => [
                'name' => 'test',
                'age' => 0,
                'changedName' => null,
                'nullable' => 1,
                'boolOrInt' => false,
                'secondTestClass' => null,
                'mixed' => null,
                'float' => null,
                'array' => null,
            ],
        ], $serializer->serialize($testClass));
    }
}