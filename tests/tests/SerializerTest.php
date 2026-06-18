<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Test\Src\Models\ArrayModel;
use Aternos\Serializer\Test\Src\Models\BackedEnumModel;
use Aternos\Serializer\Test\Src\Models\BuiltInTypeModel;
use Aternos\Serializer\Test\Src\Models\CustomSerializerModel;
use Aternos\Serializer\Test\Src\Models\DefaultValueModel;
use Aternos\Serializer\Test\Src\Models\EnumModel;
use Aternos\Serializer\Test\Src\Models\SecondModel;
use Aternos\Serializer\Test\Src\Models\SerializerModel;
use Aternos\Serializer\Test\Src\Models\FirstModel;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $serializer = new ArraySerializer();
        $this->assertSame([
            "name" => "test",
            "age" => 0,
            "notNullable" => "asd",
        ], $serializer->serialize($model));
    }

    public function testSerializeNoName(): void
    {
        $model = new SerializerModel();
        $serializer = new ArraySerializer();
        $this->expectException(MissingPropertyException::class);
        $serializer->serialize($model);
    }

    public function testSerializeNotNull(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $model->setNotNullable(null);
        $serializer = new ArraySerializer();
        $this->expectException(IncorrectTypeException::class);
        $serializer->serialize($model);
    }

    public function testSerializeOtherClass(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $secondClass = new SecondModel();
        $secondClass->setY(1);
        $model->setSecondModel($secondClass);
        $serializer = new ArraySerializer();
        $this->assertSame([
            'name' => 'test',
            'age' => 0,
            'notNullable' => 'asd',
            'secondModel' => [
                'y' => 1,
            ],
        ], $serializer->serialize($model));
    }

    public function testSerializeOtherClassJsonSerializable(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $otherModel = new FirstModel();
        $otherModel->setName('test');
        $otherModel->setNullable(1);
        $model->setModel($otherModel);
        $serializer = new ArraySerializer();
        $this->assertSame([
            'name' => 'test',
            'age' => 0,
            'notNullable' => 'asd',
            'model' => [
                'name' => 'test',
                'age' => 0,
                'changedName' => null,
                'nullable' => 1,
                'boolOrInt' => false,
                'secondModel' => null,
                'mixed' => null,
                'float' => null,
                'array' => null,
            ],
        ], $serializer->serialize($model));
    }

    public function testSerializingDefaultValueModel(): void
    {
        $serializer = new ArraySerializer();
        $model = new DefaultValueModel();
        $this->assertSame([
            "intWithDefault" => 0,
            "stringWithDefault" => "",
        ], $serializer->serialize($model));
    }

    public function testSerializeBuiltInTypes(): void
    {
        $serializer = new ArraySerializer();
        $model = new BuiltInTypeModel();
        $this->assertSame([
            "int" => null,
            "float" => null,
            "string" => null,
            "array" => null,
            "object" => null,
            "self" => null,
            "false" => null,
            "true" => null,
        ], $serializer->serialize($model));
    }

    public function testCustomSerializer(): void
    {
        $model = new CustomSerializerModel();
        $expected = '{"model":"Tzo0NjoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXE1vZGVsc1xTZWNvbmRNb2RlbCI6MDp7fQ==","testArray":["Tzo0NjoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXE1vZGVsc1xTZWNvbmRNb2RlbCI6MDp7fQ==","Tzo0NjoiQXRlcm5vc1xTZXJpYWxpemVyXFRlc3RcU3JjXE1vZGVsc1xTZWNvbmRNb2RlbCI6MDp7fQ=="]}';
        $this->assertEquals($expected, json_encode($model));
    }

    public function testCustomItemSerializerThrowsIfItemIsNotAnObject(): void
    {
        $model = new CustomSerializerModel();
        $model->setTestArray([1]);
        $this->expectException(IncorrectTypeException::class);
        json_encode($model);
    }

    public function testSerializeBackedEnum(): void
    {
        $serializer = new ArraySerializer();
        $this->assertEquals(["enum" => "a"], $serializer->serialize(new BackedEnumModel()));
    }

    public function testSerializeUnbackedEnum(): void
    {
        $serializer = new ArraySerializer();
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected 'enum' to be 'BackedEnum' found: \Aternos\Serializer\Test\Src\TestEnum::A");
        $serializer->serialize(new EnumModel());
    }

    public function testSerializeArrayItems(): void
    {
        $serializer = new ArraySerializer();
        $model = new ArrayModel();

        $model->untypedArray = [new BuiltInTypeModel()];
        $model->typedArray = [new BuiltInTypeModel()];
        $model->array = [new BuiltInTypeModel()];

        $this->assertEquals([
            "untypedArray" => [[
                "int" => null,
                "float" => null,
                "string" => null,
                "array" => null,
                "object" => null,
                "self" => null,
                "false" => null,
                "true" => null,
            ]],
            "array" => [[
                "int" => null,
                "float" => null,
                "string" => null,
                "array" => null,
                "object" => null,
                "self" => null,
                "false" => null,
                "true" => null,
            ]],
            "typedArray" => [[
                "int" => null,
                "float" => null,
                "string" => null,
                "array" => null,
                "object" => null,
                "self" => null,
                "false" => null,
                "true" => null,
            ]],
            "backedEnumArray" => ["a"],
            "stringArray" => [""],
            "intArray" => [0],
        ], $serializer->serialize($model));
    }
}
