<?php

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\BackedEnumSerializer;
use Aternos\Serializer\Exceptions\UnsupportedInputObjectException;
use Aternos\Serializer\Test\Src\Models\FirstModel;
use Aternos\Serializer\Test\Src\TestBackedEnum;
use PHPUnit\Framework\TestCase;

class BackedEnumSerializerTest extends TestCase
{
    public function testSerialize()
    {
        $serializer = new BackedEnumSerializer();

        $this->assertSame("a", $serializer->serialize(TestBackedEnum::A));
        $this->assertSame("b", $serializer->serialize(TestBackedEnum::B));
        $this->assertSame("c", $serializer->serialize(TestBackedEnum::C));
    }

    public function testSerializeInvalidInput()
    {
        $this->expectException(UnsupportedInputObjectException::class);
        $this->expectExceptionMessage("Unsupported input object 'Aternos\Serializer\Test\Src\Models\FirstModel': Only BackedEnum and UnitEnum are supported by EnumSerializer.");
        $serializer = new BackedEnumSerializer();
        $serializer->serialize(new FirstModel());
    }
}
