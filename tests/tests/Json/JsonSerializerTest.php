<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Json\JsonSerializer;
use Aternos\Serializer\Test\Src\Models\SerializerModel;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    public function testSerializeToJson()
    {
        $model = new SerializerModel();
        $model->setName('test');
        $serializer = new JsonSerializer();
        $this->assertSame('{"name":"test","age":0,"notNullable":"asd"}', $serializer->serialize($model));
    }
}
