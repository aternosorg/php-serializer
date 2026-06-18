<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Exceptions\IncorrectTypeException;
use Aternos\Serializer\Json\JsonDeserializer;
use Aternos\Serializer\Test\Src\Models\FirstModel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JsonDeserializerTest extends TestCase
{
    public function testDeserializeJson(): void
    {
        $deserializer = new JsonDeserializer(FirstModel::class);
        $model = $deserializer->deserialize('{"name":"test","age":18}');
        $this->assertSame('test', $model->getName());
        $this->assertSame(18, $model->getAge());
        $this->assertSame('test', $model->getNotAJsonField());
    }


    public function testDeserializeInvalidData(): void
    {
        $deserializer = new JsonDeserializer(FirstModel::class);
        $this->expectException(IncorrectTypeException::class);
        $this->expectExceptionMessage("Expected '.' to be 'Aternos\Serializer\Test\Src\Models\FirstModel' found: 0");
        $deserializer->deserialize("0");
    }

    public function testJsonDeserializerDataIsNotStringOrArray(): void
    {
        $deserializer = new JsonDeserializer(FirstModel::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data must be a string or an array.");
        $deserializer->deserialize(0);
    }
}
