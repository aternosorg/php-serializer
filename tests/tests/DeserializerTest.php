<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\ArrayDeserializer;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\InvalidEnumBackingException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Exceptions\UnsupportedTypeException;
use Aternos\Serializer\Json\JsonDeserializer;
use Aternos\Serializer\Test\Src\ArrayDeserializerAccessor;
use Aternos\Serializer\Test\Src\Base64Serializer;
use Aternos\Serializer\Test\Src\Models\ArrayModel;
use Aternos\Serializer\Test\Src\Models\BackedEnumModel;
use Aternos\Serializer\Test\Src\Models\BadConstructorModel;
use Aternos\Serializer\Test\Src\Models\BuiltInTypeModel;
use Aternos\Serializer\Test\Src\Models\ConstructorParamModel;
use Aternos\Serializer\Test\Src\Models\CustomSerializerInvalidTypeModel;
use Aternos\Serializer\Test\Src\Models\CustomSerializerModel;
use Aternos\Serializer\Test\Src\Models\DefaultValueModel;
use Aternos\Serializer\Test\Src\Models\EnumModel;
use Aternos\Serializer\Test\Src\Models\IntersectionCustomModel;
use Aternos\Serializer\Test\Src\Models\IntersectionModel;
use Aternos\Serializer\Test\Src\Models\ObjectTypedCustomDeserializerModel;
use Aternos\Serializer\Test\Src\Models\PrivateConstructorParamModel;
use Aternos\Serializer\Test\Src\Models\PrivateModel;
use Aternos\Serializer\Test\Src\Models\RecursiveModel;
use Aternos\Serializer\Test\Src\Models\SecondModel;
use Aternos\Serializer\Test\Src\Models\SerializerModel;
use Aternos\Serializer\Test\Src\Models\StringTypedCustomDeserializerModel;
use Aternos\Serializer\Test\Src\Models\FirstModel;
use Aternos\Serializer\Test\Src\Models\UnionBuiltinCustomDeserializerModel;
use Aternos\Serializer\Test\Src\Models\UnionIntersectionModel;
use Aternos\Serializer\Test\Src\Models\UnionObjectCustomDeserializerModel;
use Aternos\Serializer\Test\Src\Models\UntypedCustomDeserializerModel;
use Aternos\Serializer\Test\Src\TestBackedEnum;
use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DeserializerTest extends TestCase
{
    public function testDeserialize(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize(["name" => "test", "age" => 18]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(18, $model->getAge());
        $this->assertSame('test', $model->getNotAJsonField());
    }

    public function testDeserializeInvalidClass(): void
    {
        $deserializer = new ArrayDeserializer("non-existent-class");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class 'non-existent-class' does not exist.");
        $deserializer->deserialize([]);
    }

    public function testDeserializeAdditionalProperties(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "age" => 18,
            "non-existent-property" => false
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(18, $model->getAge());
    }

    public function testDeserializeRenamedProperties(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "age" => 18,
            "changedName" => "other-name"
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(18, $model->getAge());
        $this->assertSame('other-name', $model->getOriginalName());
    }

    public function testDeserializeMissingProperty(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $this->expectException(MissingPropertyException::class);
        $this->expectExceptionMessage("Missing property '.name' of type 'string'.");
        $deserializer->deserialize(["age" => 18]);
    }

    public function testDeserializeMissingPropertyNotRequired(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize(["name" => "test"]);
        $this->assertSame('test', $model->getName());
    }

    public function testDeserializeIncorrectDataType(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.age' to be 'int' found: 'eighteen'");
        $deserializer->deserialize([
            "name" => "test",
            "age" => "eighteen"
        ]);
    }

    public function testDeserializeOptionalNotNullable(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "nullable" => 0
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(0, $model->getNullable());
    }

    public function testDeserializeOptionalNotNullableNull(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.nullable' to be 'int' found: NULL");
        $deserializer->deserialize([
            "name" => "test",
            "nullable" => null
        ]);
    }

    public function testDeserializeUnionTypeBool(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => true
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertTrue($model->getBoolOrInt());
    }

    public function testDeserializeUnionTypeInt(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => 1
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(1, $model->getBoolOrInt());
    }

    public function testDeserializeUnionTypeString(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.boolOrInt' to be 'int|bool' found: 'not-either'");
        $deserializer->deserialize([
            "name" => "test",
            "boolOrInt" => "not-either"
        ]);
    }

    public function testDeserializeSecondClass(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "secondModel" => ["y" => 123]
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(123, $model->getSecondModel()?->getY());
    }

    public function testDeserializeMixed(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "mixed" => ["y" => 123]
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(["y" => 123], $model->getMixed());
    }

    public function testDeserializeFloat(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "float" => 1.5
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(1.5, $model->getFloat());
    }

    public function testDeserializeIntAsFloat(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "float" => 1
        ]);
        $this->assertSame('test', $model->getName());
        $this->assertSame(1.0, $model->getFloat());
    }

    public function testDeserializeSecondClassNotArray(): void
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.secondModel' to be 'Aternos\Serializer\Test\Src\Models\SecondModel' found: 'y'");
        $deserializer->deserialize([
            "name" => "test",
            "secondModel" => "y"
        ]);
    }

    public function testDeserializeIntersection(): void
    {
        $deserializer = new ArrayDeserializer(IntersectionModel::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Throwable&Iterator' for property '.x': Intersection types are not supported");
        $deserializer->deserialize([
            "x" => "123"
        ]);
    }

    public function testDeserializeUnionIntersection(): void
    {
        $deserializer = new ArrayDeserializer(UnionIntersectionModel::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Throwable&Iterator' for property '.x': Intersection types are not supported");
        $deserializer->deserialize([
            "x" => "123"
        ]);
    }

    public function testDeserializeArray()
    {
        $deserializer = new ArrayDeserializer(FirstModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "array" => [1, 2, 3]
        ]);
        $this->assertSame([1, 2, 3], $model->getArray());
    }

    public function testDeserializeNullableObjects()
    {
        $deserializer = new ArrayDeserializer(SerializerModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "secondModel" => null,
            "model" => null
        ]);
        $this->assertNull($model->getSecondModel());
    }

    public function testDeserializeAllowNull()
    {
        $deserializer = new ArrayDeserializer(SerializerModel::class);
        $model = $deserializer->deserialize([
            "name" => "test",
            "age" => 15,
            "nullable" => null
        ]);
        $this->assertFalse(isset($model->nullable));
    }

    public function testDeserializeIntWithDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueModel::class);
        $model = $deserializer->deserialize([
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertTrue(isset($model->intWithDefault));
        $this->assertSame(0, $model->intWithDefault);
    }

    public function testDeserializeIntWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueModel::class);
        $model = $deserializer->deserialize([
            "intWithDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertFalse(isset($model->intWithoutDefault));
    }

    public function testDeserializeNullableIntWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueModel::class);
        $model = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertNull($model->nullableIntWithoutDefault);
    }

    public function testDeserializeStringWithDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueModel::class);
        $model = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithoutDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertTrue(isset($model->stringWithDefault));
        $this->assertSame("", $model->stringWithDefault);
    }

    public function testDeserializeStringWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueModel::class);
        $model = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "nullableStringWithoutDefault" => "test"
        ]);
        $this->assertFalse(isset($model->stringWithoutDefault));
    }

    public function testDeserializeNullableStringWithoutDefault(): void
    {
        $deserializer = new ArrayDeserializer(DefaultValueModel::class);
        $model = $deserializer->deserialize([
            "intWithDefault" => 1,
            "intWithoutDefault" => 1,
            "nullableIntWithoutDefault" => 1,
            "stringWithDefault" => "test",
            "stringWithoutDefault" => "test"
        ]);
        $this->assertNull($model->nullableStringWithoutDefault);
    }

    public function testDeserializeNullType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "null" => null
        ]);
        $this->assertNull($model->null);
    }

    public function testDeserializeIntType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "int" => 1
        ]);
        $this->assertSame(1, $model->int);
    }

    public function testDeserializeFloatType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "float" => 1.5
        ]);
        $this->assertSame(1.5, $model->float);
    }

    public function testDeserializeStringType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "string" => "test"
        ]);
        $this->assertSame("test", $model->string);
    }

    public function testDeserializeArrayType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "array" => [1, 2, 3]
        ]);
        $this->assertSame([1, 2, 3], $model->array);
    }

    public function testDeserializeObjectType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "object" => (object)["key" => "value"]
        ]);
        $this->assertEquals((object)["key" => "value"], $model->object);
    }

    public function testDeserializeSelfType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "self" => [
                "int" => 1,
            ]
        ]);
        $this->assertInstanceOf(BuiltInTypeModel::class, $model->self);
        $this->assertSame(1, $model->self->int);
    }

    public function testDeserializeFalseType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "false" => false
        ]);
        $this->assertFalse($model->false);
    }

    public function testDeserializeTrueType(): void
    {
        $deserializer = new ArrayDeserializer(BuiltInTypeModel::class);
        $model = $deserializer->deserialize([
            "true" => true
        ]);
        $this->assertTrue($model->true);
    }

    public function testDeserializeUntypedArray(): void
    {
        $deserializer = new ArrayDeserializer(ArrayModel::class);
        $model = $deserializer->deserialize([
            "untypedArray" => [
                ["int" => 1]
            ]
        ]);
        $expected = new BuiltInTypeModel();
        $expected->int = 1;
        $this->assertEquals([$expected], $model->untypedArray);
    }

    public function testDeserializeArrayWithoutItemType(): void
    {
        $deserializer = new ArrayDeserializer(ArrayModel::class);
        $model = $deserializer->deserialize([
            "array" => [
                ["int" => 1]
            ]
        ]);
        $this->assertEquals([["int" => 1]], $model->array);
    }

    public function testDeserializeTypedArray(): void
    {
        $deserializer = new ArrayDeserializer(ArrayModel::class);
        $model = $deserializer->deserialize([
            "typedArray" => [
                ["int" => 1]
            ]
        ]);
        $expected = new BuiltInTypeModel();
        $expected->int = 1;
        $this->assertEquals([$expected], $model->typedArray);
    }

    public function testDeserializeTypedArrayPreservesKeys(): void
    {
        $deserializer = new ArrayDeserializer(ArrayModel::class);
        $model = $deserializer->deserialize([
            "typedArray" => [
                "test" => ["int" => 1]
            ]
        ]);
        $expected = new BuiltInTypeModel();
        $expected->int = 1;
        $this->assertEquals(["test" => $expected], $model->typedArray);
    }

    public function testUnknownBuiltInType(): void
    {
        $deserializer = new ArrayDeserializerAccessor(FirstModel::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'not-a-real-type' for property '.name'");
        $deserializer->isBuiltInTypeValid("not-a-real-type", "test", ".name");
    }

    public function testArrayDeserializerArgumentIsNotAnArray(): void
    {
        $deserializer = new ArrayDeserializerAccessor(FirstModel::class);
        $this->expectException(IncorrectTypeException::class);
        $deserializer->deserialize("not-an-array");
    }

    public function testCustomDeserializer(): void
    {

        $deserializer = new JsonDeserializer(CustomSerializerModel::class);
        $model = $deserializer->deserialize(json_encode(new CustomSerializerModel()));
        $this->assertInstanceOf(CustomSerializerModel::class, $model);
        $this->assertInstanceOf(SecondModel::class, $model->getModel());
        $this->assertIsArray($model->getTestArray());
        $this->assertInstanceOf(SecondModel::class, $model->getTestArray()[0]);
        $this->assertInstanceOf(SecondModel::class, $model->getTestArray()[1]);
    }

    public function testCustomDeserializerReturnsInvalidType(): void
    {
        $deserializer = new JsonDeserializer(CustomSerializerInvalidTypeModel::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.model' to be 'Aternos\Serializer\Test\Src\Models\FirstModel' found: \Aternos\Serializer\Test\Src\Models\BuiltInTypeModel::");
        $deserializer->deserialize('{"model":"' . (new Base64Serializer())->serialize(new BuiltInTypeModel()) . '"}');
    }

    public function testDeserializeBackedEnum(): void
    {
        $deserializer = new ArrayDeserializerAccessor(BackedEnumModel::class);
        $this->assertEquals(TestBackedEnum::A, $deserializer->deserialize(["enum" => "a"])->getEnum());
    }

    public function testDeserializeUnbackedEnum(): void
    {
        $deserializer = new ArrayDeserializerAccessor(EnumModel::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Aternos\Serializer\Test\Src\TestEnum' for property '.enum': Enums must be backed by a scalar type.");
        $deserializer->deserialize(["enum" => "a"]);
    }

    public function testDeserializeEnumWithInvalidBackingValue(): void
    {
        $deserializer = new ArrayDeserializerAccessor(BackedEnumModel::class);
        $this->expectException(InvalidEnumBackingException::class);
        $this->expectExceptionMessage("Invalid backing value for enum 'Aternos\Serializer\Test\Src\TestBackedEnum' expected: type 'string' (a, b, c) found: 'd'");
        $deserializer->deserialize(["enum" => "d"]);
    }

    public function testDeserializeEnumWithInvalidBackingType(): void
    {
        $deserializer = new ArrayDeserializerAccessor(TestBackedEnum::class);
        $this->expectException(InvalidEnumBackingException::class);
        $this->expectExceptionMessage("Invalid backing value for enum 'Aternos\Serializer\Test\Src\TestBackedEnum' expected: type 'string' (a, b, c) found: 0");
        $this->assertEquals(TestBackedEnum::A, $deserializer->deserialize(0));
    }

    public function testCreatePrivateConstructor(): void
    {
        $deserializer = new ArrayDeserializer(PrivateModel::class);
        $this->assertEquals("test", $deserializer->deserialize(["name" => "test"])->getName());
    }

    public function testConstructorWithArgs(): void
    {
        $deserializer = new ArrayDeserializer(ConstructorParamModel::class);
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
        $deserializer = new ArrayDeserializer(BadConstructorModel::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Aternos\Serializer\Test\Src\Models\BadConstructorModel' for property '': Required parameter 'x' not annotated as serializable");
        $deserializer->deserialize([]);
    }

    public function testCreatePrivateConstructorWithArgs(): void
    {
        $deserializer = new ArrayDeserializer(PrivateConstructorParamModel::class);
        $result = $deserializer->deserialize(["name" => "test", "age" => 42]);
        $this->assertEquals("test", $result->getName());
        $this->assertEquals(42, $result->getAge());
    }

    public function testDeserializeClosureThrowsUnsupportedType(): void
    {
        $deserializer = new ArrayDeserializer(Closure::class);
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage("Unsupported type 'Closure' for property '': Class Closure is an internal class marked as final that cannot be instantiated without invoking its constructor");
        $deserializer->deserialize([]);
    }

    public function testDeserializeRecursive(): void
    {
        $deserializer = new ArrayDeserializer(RecursiveModel::class);

        $root = $deserializer->deserialize(["x" => 1, "next" => ["x" => 2]]);
        $this->assertInstanceOf(RecursiveModel::class, $root);
        $this->assertEquals(1, $root->getX());

        $next = $root->getNext();
        $this->assertInstanceOf(RecursiveModel::class, $next);
        $this->assertEquals(2, $next->getX());

        $this->assertNull($next->getNext());
    }

    public function testIsTypeValidNullType(): void
    {
        $deserializer = new ArrayDeserializer(UntypedCustomDeserializerModel::class);
        $result = $deserializer->deserialize(["value" => "anything"]);
        $this->assertInstanceOf(SecondModel::class, $result->getValue());
    }

    public function testIsTypeValidNullValueNullableType(): void
    {
        $accessor = new ArrayDeserializerAccessor(RecursiveModel::class);
        $type = (new \ReflectionClass(RecursiveModel::class))->getProperty("next")->getType();
        $this->assertTrue($accessor->isTypeValid($type, null, ""));
    }

    public function testIsTypeValidBuiltinValid(): void
    {
        $deserializer = new ArrayDeserializer(ObjectTypedCustomDeserializerModel::class);
        $result = $deserializer->deserialize(["value" => "anything"]);
        $this->assertInstanceOf(SecondModel::class, $result->getValue());
    }

    public function testIsTypeValidBuiltinInvalid(): void
    {
        $deserializer = new ArrayDeserializer(StringTypedCustomDeserializerModel::class);
        $this->expectException(IncorrectTypeException::class);
        $deserializer->deserialize(["value" => "anything"]);
    }

    public function testIsTypeValidUnionValid(): void
    {
        $deserializer = new ArrayDeserializer(UnionObjectCustomDeserializerModel::class);
        $result = $deserializer->deserialize(["value" => "anything"]);
        $this->assertInstanceOf(SecondModel::class, $result->getValue());
    }

    public function testIsTypeValidUnionInvalid(): void
    {
        $deserializer = new ArrayDeserializer(UnionBuiltinCustomDeserializerModel::class);
        $this->expectException(IncorrectTypeException::class);
        $deserializer->deserialize(["value" => "anything"]);
    }

    public function testIsTypeValidIntersectionInvalid(): void
    {
        $accessor = new ArrayDeserializerAccessor(IntersectionModel::class);
        $type = (new \ReflectionClass(IntersectionModel::class))->getProperty("x")->getType();
        $this->assertFalse($accessor->isTypeValid($type, new \Exception(), ""));
    }

    public function testDeserializeIntersectionCustom(): void
    {
        $deserializer = new ArrayDeserializer(IntersectionCustomModel::class);
        $this->assertInstanceOf(IntersectionCustomModel::class, $deserializer->deserialize(["x" => []]));
    }
}
