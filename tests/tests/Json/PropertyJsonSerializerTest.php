<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Exceptions\MissingPropertyException;
use Aternos\Serializer\Test\Src\Models\SecondModel;
use Aternos\Serializer\Test\Src\Models\SerializerModel;
use JsonException;
use PHPUnit\Framework\TestCase;

class PropertyJsonSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $this->assertSame('{"name":"test","age":0,"notNullable":"asd"}', json_encode($model));
    }

    public function testSerializeNoName(): void
    {
        $model = new SerializerModel();
        $this->expectException(MissingPropertyException::class);
        json_encode($model);
    }

    public function testSerializeNotNull(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $model->setNotNullable(null);
        $this->expectException(IncorrectTypeException::class);
        json_encode($model);
    }

    public function testSerializeOtherClass(): void
    {
        $model = new SerializerModel();
        $model->setName('test');
        $secondClass = new SecondModel();
        $secondClass->setY(1);
        $model->setSecondModel($secondClass);
        $this->assertSame('{"name":"test","age":0,"notNullable":"asd","secondModel":{"y":1}}', json_encode($model));
    }

    public function testFromJson(): void
    {
        $model = SerializerModel::fromJson('{"name":"test","age":0,"notNullable":"asd"}');
        $this->assertSame("test", $model->getName());
        $this->assertSame(0, $model->getAge());
        $this->assertSame("asd", $model->getNotNullable());
    }

    public function testTryFromJson(): void
    {
        $model = SerializerModel::tryFromJson('{"name":"test","age":0,"notNullable":"asd"}');
        $this->assertNotNull($model);
        $this->assertSame("test", $model->getName());
        $this->assertSame(0, $model->getAge());
        $this->assertSame("asd", $model->getNotNullable());
    }

    public function testFromJsonInvalidJson(): void
    {
        $this->expectException(JsonException::class);
        SerializerModel::fromJson('{');
    }

    public function testTryFromJsonInvalidJson(): void
    {
        $this->assertNull(SerializerModel::tryFromJson('{'));
    }

    public function testFromJsonMissingProperty(): void
    {
        $this->expectException(MissingPropertyException::class);
        SerializerModel::fromJson('{}');
    }

    public function testTryFromJsonMissingProperty(): void
    {
        $this->assertNull(SerializerModel::tryFromJson('{}'));
    }

    public function testFromJsonIncorrectType(): void
    {
        $this->expectException(IncorrectTypeException::class);
        SerializerModel::fromJson('{"name":1}');
    }

    public function testTryFromJsonIncorrectType(): void
    {
        $this->assertNull(SerializerModel::tryFromJson('{"name":1}'));
    }
}
