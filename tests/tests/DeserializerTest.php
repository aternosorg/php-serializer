<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\ArrayDeserializer;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\InvalidEnumBackingException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use Aternos\Serializer\Json\JsonDeserializer;
use Aternos\Serializer\Test\Src\ArrayDeserializerAccessor;
use Aternos\Serializer\Test\Src\ArrayTests;
use Aternos\Serializer\Test\Src\BackedEnumTestClass;
use Aternos\Serializer\Test\Src\BadConstructorTestClass;
use Aternos\Serializer\Test\Src\BuiltInTypeTestClass;
use Aternos\Serializer\Test\Src\ConstructorParamTestClass;
use Aternos\Serializer\Test\Src\CustomSerializerInvalidTypeTestClass;
use Aternos\Serializer\Test\Src\CustomSerializerTestClass;
use Aternos\Serializer\Test\Src\DefaultValueTestClass;
use Aternos\Serializer\Test\Src\EnumTestClass;
use Aternos\Serializer\Test\Src\IntersectionTestClass;
use Aternos\Serializer\Test\Src\ObjectDeserializer;
use Aternos\Serializer\Test\Src\ObjectTypedCustomDeserializerTestClass;
use Aternos\Serializer\Test\Src\PrivateConstructorParamTestClass;
use Aternos\Serializer\Test\Src\PrivateTestClass;
use Aternos\Serializer\Test\Src\StringTypedCustomDeserializerTestClass;
use Aternos\Serializer\Test\Src\ThrowableIterator;
use Aternos\Serializer\Test\Src\UnionBuiltinCustomDeserializerTestClass;
use Aternos\Serializer\Test\Src\UnionObjectCustomDeserializerTestClass;
use Aternos\Serializer\Test\Src\UntypedCustomDeserializerTestClass;
use Aternos\Serializer\Test\Src\RecursiveTestClass;
use Aternos\Serializer\Test\Src\SecondTestClass;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use Aternos\Serializer\Test\Src\TestBackedEnum;
use Aternos\Serializer\Test\Src\TestClass;
use Aternos\Serializer\Test\Src\UnionIntersectionTestClass;
use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DeserializerTest extends TestCase
{
    public function testDeserialize(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize(["name" => "test", "age" => 18]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(18, $testClass->getAge());
        $this->assertSame('test', $testClass->getNotAJsonField());
    }

    public function testDeserializeInvalidClass(): void
    {
        $deserializer = new ArrayDeserializer("non-existent-class");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs("Class 'non-existent-class' does not exist.");
        $deserializer->deserialize([]);
    }

    public function testDeserializeAdditionalProperties(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
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
        $deserializer = new ArrayDeserializer(TestClass::class);
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
        $deserializer = new ArrayDeserializer(TestClass::class);
        $this->expectException(MissingPropertyException::class);
        $this->expectExceptionMessageIs("Missing property '.name' of type 'string'.");
        $deserializer->deserialize(["age" => 18]);
    }

    public function testDeserializeMissingPropertyNotRequired(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize(["name" => "test"]);
        $this->assertSame('test', $testClass->getName());
    }

    public function testDeserializeIncorrectDataType(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessageIs("Expected '.age' to be 'int' found: 'eighteen'");
        $deserializer->deserialize([
            "name" => "test",
            "age" => "eighteen"
        ]);
    }

    public function testDeserializeOptionalNotNullable(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "nullable" => 0
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(0, $testClass->getNullable());
    }

    public function testDeserializeOptionalNotNullableNull(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessageIs("Expected '.nullable' to be 'int' found: NULL");
        $deserializer->deserialize([
            "name" => "test",
            "nullable" => null
        ]);
    }

    public function testDeserializeUnionTypeBool(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => true
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertTrue($testClass->getBoolOrInt());
    }

    public function testDeserializeUnionTypeInt(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => 1
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(1, $testClass->getBoolOrInt());
    }

    public function testDeserializeUnionTypeString(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessageIs("Expected '.boolOrInt' to be 'int|bool' found: 'not-either'");
        $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => "not-either"
        ]);
    }

    public function testDeserializeSecondClass(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "secondTestClass" => ["y" => 123]
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(123, $testClass->getSecondTestClass()?->getY());
    }

    public function testDeserializeMixed(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "mixed" => ["y" => 123]
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(["y" => 123], $testClass->getMixed());
    }

    public function testDeserializeFloat(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "float" => 1.5
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(1.5, $testClass->getFloat());
    }

    public function testDeserializeIntAsFloat(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "float" => 1
        ]);
        $this->assertSame('test', $testClass->getName());
        $this->assertSame(1.0, $testClass->getFloat());
    }

    public function testDeserializeSecondClassNotArray(): void
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessageIs("Expected '.secondTestClass' to be 'Aternos\Serializer\Test\Src\SecondTestClass' found: 'y'");
        $deserializer->deserialize([
            "name" => "test",
            "secondTestClass" => "y"
        ]);
    }

    public function testDeserializeIntersection(): void
    {
        $deserializer = new ArrayDeserializer(IntersectionTestClass::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessageIs("Unsupported type 'Throwable&Iterator' for property '.x': Intersection types are not supported");
        $deserializer->deserialize([
            "x" => "123"
        ]);
    }

    public function testDeserializeUnionIntersection(): void
    {
        $deserializer = new ArrayDeserializer(UnionIntersectionTestClass::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessageIs("Unsupported type 'Throwable&Iterator' for property '.x': Intersection types are not supported");
        $deserializer->deserialize([
            "x" => "123"
        ]);
    }

    public function testDeserializeArray()
    {
        $deserializer = new ArrayDeserializer(TestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "array" => [1, 2, 3]
        ]);
        $this->assertSame([1, 2, 3], $testClass->getArray());
    }

    public function testDeserializeNullableObjects()
    {
        $deserializer = new ArrayDeserializer(SerializerTestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "secondTestClass" => null,
            "testClass" => null
        ]);
        $this->assertNull($testClass->getSecondTestClass());
    }

    public function testDeserializeAllowNull()
    {
        $deserializer = new ArrayDeserializer(SerializerTestClass::class);
        $testClass = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "nullable" => null
        ]);
        $this->assertFalse(isset($testClass->nullable));
    }

    public function testDeserializeIntWithDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueTestClass::class);
        $testClass = $deserializer->deserialize([
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertTrue(isset($testClass->intWithDefault));
        $this->assertSame(0, $testClass->intWithDefault);
    }

    public function testDeserializeIntWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueTestClass::class);
        $testClass = $deserializer->deserialize([
            "intWithDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertFalse(isset($testClass->intWithoutDefault));
    }

    public function testDeserializeNullableIntWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueTestClass::class);
        $testClass = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertNull($testClass->nullableIntWithoutDefault);
    }

    public function testDeserializeStringWithDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueTestClass::class);
        $testClass = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertTrue(isset($testClass->stringWithDefault));
        $this->assertSame("", $testClass->stringWithDefault);
    }

    public function testDeserializeStringWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueTestClass::class);
        $testClass = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertFalse(isset($testClass->stringWithoutDefault));
    }

    public function testDeserializeNullableStringWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueTestClass::class);
        $testClass = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test"
        ]);
        $this->assertNull($testClass->nullableStringWithoutDefault);
    }

    public function testDeserializeNullType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "null" => null
        ]);
        $this->assertNull($testClass->null);
    }

    public function testDeserializeIntType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "int" => 1
        ]);
        $this->assertSame(1, $testClass->int);
    }

    public function testDeserializeFloatType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "float" => 1.5
        ]);
        $this->assertSame(1.5, $testClass->float);
    }

    public function testDeserializeStringType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "string" => "test"
        ]);
        $this->assertSame("test", $testClass->string);
    }

    public function testDeserializeArrayType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "array" => [1, 2, 3]
        ]);
        $this->assertSame([1, 2, 3], $testClass->array);
    }

    public function testDeserializeObjectType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "object" => (object)["key" => "value"]
        ]);
        $this->assertEquals((object)["key" => "value"], $testClass->object);
    }

    public function testDeserializeSelfType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "self" => [
                "int" => 1,
            ]
        ]);
        $this->assertInstanceOf(BuiltInTypeTestClass::class, $testClass->self);
        $this->assertSame(1, $testClass->self->int);
    }

    public function testDeserializeFalseType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "false" => false
        ]);
        $this->assertFalse($testClass->false);
    }

    public function testDeserializeTrueType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeTestClass::class);
        $testClass = $deserializer->deserialize([
            "true" => true
        ]);
        $this->assertTrue($testClass->true);
    }

    public function testDeserializeUntypedArray(): void
    {
        $deserializer = new ArrayDeserializer(ArrayTests::class);
        $testClass = $deserializer->deserialize([
            "untypedArray" => [
                ["int" => 1]
            ]
        ]);
        $expected = new BuiltInTypeTestClass();
        $expected->int = 1;
        $this->assertEquals([$expected], $testClass->untypedArray);
    }

    public function testDeserializeArrayWithoutItemType(): void
    {
        $deserializer = new ArrayDeserializer(ArrayTests::class);
        $testClass = $deserializer->deserialize([
            "array" => [
                ["int" => 1]
            ]
        ]);
        $this->assertEquals([["int" => 1]], $testClass->array);
    }

    public function testDeserializeTypedArray(): void
    {
        $deserializer = new ArrayDeserializer(ArrayTests::class);
        $testClass = $deserializer->deserialize([
            "typedArray" => [
                ["int" => 1]
            ]
        ]);
        $expected = new BuiltInTypeTestClass();
        $expected->int = 1;
        $this->assertEquals([$expected], $testClass->typedArray);
    }

    public function testDeserializeTypedArrayPreservesKeys(): void
    {
        $deserializer = new ArrayDeserializer(ArrayTests::class);
        $testClass = $deserializer->deserialize([
            "typedArray" => [
                "test" => ["int" => 1]
            ]
        ]);
        $expected = new BuiltInTypeTestClass();
        $expected->int = 1;
        $this->assertEquals(["test" => $expected], $testClass->typedArray);
    }

    public function testUnknownBuiltInType(): void
    {
        $deserializer = new ArrayDeserializerAccessor(TestClass::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessageIs("Unsupported type 'not-a-real-type' for property '.name'");
        $deserializer->isBuiltInTypeValid("not-a-real-type", "test", ".name");
    }

    public function testArrayDeserializerArgumentIsNotAnArray(): void
    {
        $deserializer = new ArrayDeserializerAccessor(TestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $deserializer->deserialize("not-an-array");
    }

    public function testCustomDeserializer(): void
    {
        $deserializer = new JsonDeserializer(CustomSerializerTestClass::class);
        $testClass = $deserializer->deserialize('{"testClass":"Tzo0MzoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXFNlY29uZFRlc3RDbGFzcyI6MDp7fQ==","testArray":["Tzo0MzoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXFNlY29uZFRlc3RDbGFzcyI6MDp7fQ==","Tzo0MzoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXFNlY29uZFRlc3RDbGFzcyI6MDp7fQ=="]}');
        $this->assertInstanceOf(CustomSerializerTestClass::class, $testClass);
        $this->assertInstanceOf(SecondTestClass::class, $testClass->getTestClass());
        $this->assertIsArray($testClass->getTestArray());
        $this->assertInstanceOf(SecondTestClass::class, $testClass->getTestArray()[0]);
        $this->assertInstanceOf(SecondTestClass::class, $testClass->getTestArray()[1]);
    }

    public function testCustomDeserializerReturnsInvalidType(): void
    {
        $deserializer = new JsonDeserializer(CustomSerializerInvalidTypeTestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessageIsOrContains("Expected '.testClass' to be 'Aternos\Serializer\Test\Src\TestClass' found: \Aternos\Serializer\Test\Src\BuiltInTypeTestClass::");
        $deserializer->deserialize('{"testClass":"Tzo0ODoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXEJ1aWx0SW5UeXBlVGVzdENsYXNzIjo4OntzOjM6ImludCI7TjtzOjU6ImZsb2F0IjtOO3M6Njoic3RyaW5nIjtOO3M6NToiYXJyYXkiO047czo2OiJvYmplY3QiO047czo0OiJzZWxmIjtOO3M6NToiZmFsc2UiO047czo0OiJ0cnVlIjtOO30="}');
    }

    public function testDeserializeBackedEnum(): void
    {
        $deserializer = new ArrayDeserializerAccessor(BackedEnumTestClass::class);
        $this->assertEquals(TestBackedEnum::A, $deserializer->deserialize(["enum" => "a"])->getEnum());
    }

    public function testDeserializeUnbackedEnum(): void
    {
        $deserializer = new ArrayDeserializerAccessor(EnumTestClass::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessageIs("Unsupported type 'Aternos\Serializer\Test\Src\TestEnum' for property '.enum': Enums must be backed by a scalar type.");
        $deserializer->deserialize(["enum" => "a"]);
    }

    public function testDeserializeEnumWithInvalidBackingValue(): void
    {
        $deserializer = new ArrayDeserializerAccessor(BackedEnumTestClass::class);
        $this->expectException(InvalidEnumBackingException::class);
        $this->expectExceptionMessageIs("Invalid backing value for enum 'Aternos\Serializer\Test\Src\TestBackedEnum' expected: type 'string' (a, b, c) found: 'd'");
        $deserializer->deserialize(["enum" => "d"]);
    }

    public function testDeserializeEnumWithInvalidBackingType(): void
    {
        $deserializer = new ArrayDeserializerAccessor(TestBackedEnum::class);
        $this->expectException(InvalidEnumBackingException::class);
        $this->expectExceptionMessageIs("Invalid backing value for enum 'Aternos\Serializer\Test\Src\TestBackedEnum' expected: type 'string' (a, b, c) found: 0");
        $this->assertEquals(TestBackedEnum::A, $deserializer->deserialize(0));
    }

    public function testCreatePrivateConstructor(): void
    {
        $deserializer = new ArrayDeserializer(PrivateTestClass::class);
        $this->assertEquals("test", $deserializer->deserialize(["name" => "test"])->getName());
    }

    public function testConstructorWithArgs(): void
    {
        $deserializer = new ArrayDeserializer(ConstructorParamTestClass::class);
        $result = $deserializer->deserialize([
            "param" => "a",
            "promoted" => "b",
            "nonParam" => "c",
        ]);
        $this->assertEquals("a", $result->getParam());
        $this->assertEquals("b", $result->getPromoted());
        $this->assertEquals("1", $result->getOptionalParam());
        $this->assertEquals("2", $result->getOptionalPromoted());
        $this->assertEquals("c", $result->getNonParam());
    }

    /**
     * The constructor of this test class has a required parameter that's not annotated as serializable
     * @return void
     */
    public function testBadConstructor(): void
    {
        $deserializer = new ArrayDeserializer(BadConstructorTestClass::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessageIs("Unsupported type 'Aternos\Serializer\Test\Src\BadConstructorTestClass' for property '': Required parameter 'x' not annotated as serializable");
        $deserializer->deserialize([]);
    }

    public function testCreatePrivateConstructorWithArgs(): void
    {
        $deserializer = new ArrayDeserializer(PrivateConstructorParamTestClass::class);
        $result = $deserializer->deserialize(["name" => "test", "age" => 42]);
        $this->assertEquals("test", $result->getName());
        $this->assertEquals(42, $result->getAge());
    }

    public function testDeserializeClosureThrowsUnsupportedType(): void
    {
        $deserializer = new ArrayDeserializer(Closure::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessageIs("Unsupported type 'Closure' for property '': Class Closure is an internal class marked as final that cannot be instantiated without invoking its constructor");
        $deserializer->deserialize([]);
    }

    public function testDeserializeRecursive(): void
    {
        $deserializer = new ArrayDeserializer(RecursiveTestClass::class);

        $root = $deserializer->deserialize(["x" => 1, "next" => ["x" => 2]]);
        $this->assertInstanceOf(RecursiveTestClass::class, $root);
        $this->assertEquals(1, $root->getX());

        $next = $root->getNext();
        $this->assertInstanceOf(RecursiveTestClass::class, $next);
        $this->assertEquals(2, $next->getX());

        $this->assertNull($next->getNext());
    }

    public function testIsTypeValidNullType(): void
    {
        $deserializer = new ArrayDeserializer(UntypedCustomDeserializerTestClass::class);
        $result = $deserializer->deserialize(["value" => "anything"]);
        $this->assertInstanceOf(SecondTestClass::class, $result->getValue());
    }

    public function testIsTypeValidNullValueNullableType(): void
    {
        $accessor = new ArrayDeserializerAccessor(RecursiveTestClass::class);
        $type = (new \ReflectionClass(RecursiveTestClass::class))->getProperty("next")->getType();
        $this->assertTrue($accessor->isTypeValid($type, null, ""));
    }

    public function testIsTypeValidBuiltinValid(): void
    {
        $deserializer = new ArrayDeserializer(ObjectTypedCustomDeserializerTestClass::class);
        $result = $deserializer->deserialize(["value" => "anything"]);
        $this->assertInstanceOf(SecondTestClass::class, $result->getValue());
    }

    public function testIsTypeValidBuiltinInvalid(): void
    {
        $deserializer = new ArrayDeserializer(StringTypedCustomDeserializerTestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $deserializer->deserialize(["value" => "anything"]);
    }

    public function testIsTypeValidUnionValid(): void
    {
        $deserializer = new ArrayDeserializer(UnionObjectCustomDeserializerTestClass::class);
        $result = $deserializer->deserialize(["value" => "anything"]);
        $this->assertInstanceOf(SecondTestClass::class, $result->getValue());
    }

    public function testIsTypeValidUnionInvalid(): void
    {
        $deserializer = new ArrayDeserializer(UnionBuiltinCustomDeserializerTestClass::class);
        $this->expectException(IncorrectTypeException::class);
        $deserializer->deserialize(["value" => "anything"]);
    }

    public function testIsTypeValidIntersectionValid(): void
    {
        $accessor = new ArrayDeserializerAccessor(IntersectionTestClass::class);
        $type = (new \ReflectionClass(IntersectionTestClass::class))->getProperty("x")->getType();
        $this->assertTrue($accessor->isTypeValid($type, new ThrowableIterator(), ""));
    }

    public function testIsTypeValidIntersectionInvalid(): void
    {
        $accessor = new ArrayDeserializerAccessor(IntersectionTestClass::class);
        $type = (new \ReflectionClass(IntersectionTestClass::class))->getProperty("x")->getType();
        $this->assertFalse($accessor->isTypeValid($type, new \Exception(), ""));
    }
}
