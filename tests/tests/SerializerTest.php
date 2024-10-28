<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\BuiltInTypeTestClass;
use Aternos\Serializer\Test\Src\CustomSerializerTestClass;
use Aternos\Serializer\Test\Src\DefaultValueTestClass;
use Aternos\Serializer\Test\Src\SecondTestClass;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use Aternos\Serializer\Test\Src\TestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArraySerializer::class)]
#[UsesClass(PropertyJsonSerializer::class)]
#[UsesClass(Serialize::class)]
#[UsesClass(IncorrectTypeException::class)]
#[UsesClass(MissingPropertyException::class)]
class SerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $serializer = new ArraySerializer();
        $this->assertSame([
            "name" => "test",
            "age" => 0,
            "notNullable" => "asd",
        ], $serializer->serialize($testClass));
    }

    public function testSerializeNoName(): void
    {
        $testClass = new SerializerTestClass();
        $serializer = new ArraySerializer();
        $this->expectException(MissingPropertyException::class);
        $serializer->serialize($testClass);
    }

    public function testSerializeNotNull(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $testClass->setNotNullable(null);
        $serializer = new ArraySerializer();
        $this->expectException(IncorrectTypeException::class);
        $serializer->serialize($testClass);
    }

    public function testSerializeOtherClass(): void
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $secondClass = new SecondTestClass();
        $secondClass->setY(1);
        $testClass->setSecondTestClass($secondClass);
        $serializer = new ArraySerializer();
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
        $serializer = new ArraySerializer();
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

    public function testSerializingDefaultValueTestClass(): void
    {
        $serializer = new ArraySerializer();
        $testClass = new DefaultValueTestClass();
        $this->assertSame([
            "intWithDefault" => 0,
            "stringWithDefault" => "",
        ], $serializer->serialize($testClass));
    }

    public function testSerializeBuiltInTypes(): void
    {
        $serializer = new ArraySerializer();
        $testClass = new BuiltInTypeTestClass();
        $this->assertSame([
            "int" => null,
            "float" => null,
            "string" => null,
            "array" => null,
            "object" => null,
            "self" => null,
            "false" => null,
            "true" => null,
        ], $serializer->serialize($testClass));
    }

    public function testCustomSerializer(): void
    {
        $testClass = new CustomSerializerTestClass();
        $expected = '{"testClass":"TzozNzoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXFRlc3RDbGFzcyI6OTp7czo2OiIAKgBhZ2UiO2k6MDtzOjE1OiIAKgBvcmlnaW5hbE5hbWUiO047czoxMToiACoAbnVsbGFibGUiO047czoxMjoiACoAYm9vbE9ySW50IjtiOjA7czoxNjoiACoAbm90QUpzb25GaWVsZCI7czo0OiJ0ZXN0IjtzOjE4OiIAKgBzZWNvbmRUZXN0Q2xhc3MiO047czo4OiIAKgBtaXhlZCI7TjtzOjg6IgAqAGZsb2F0IjtOO3M6ODoiACoAYXJyYXkiO047fQ=="}';
        $this->assertEquals($expected, json_encode($testClass));
    }
}
