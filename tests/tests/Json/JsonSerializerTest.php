<?php

namespace Aternos\Serializer\Test\Tests\Json;

use Aternos\Serializer\Json\JsonSerializer;
use Aternos\Serializer\Serialize;
use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\Test\Src\SerializerTestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonSerializer::class)]
#[UsesClass(Serialize::class)]
#[UsesClass(ArraySerializer::class)]
class JsonSerializerTest extends TestCase
{
    public function testSerializeToJson()
    {
        $testClass = new SerializerTestClass();
        $testClass->setName('test');
        $serializer = new JsonSerializer();
        $this->assertSame('{"name":"test","age":0,"notNullable":"asd"}', $serializer->serialize($testClass));
    }
}
