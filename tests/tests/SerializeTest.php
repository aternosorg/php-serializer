<?php

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\TestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[CoversClass(Serialize::class)]
class SerializeTest extends TestCase
{
    #[Serialize]
    protected string $name = "name";

    #[Serialize("other_name", required: true, allowNull: false)]
    protected string $otherName = "other-name";

    protected string $nonSerializedName = "this isn't serialized";

    public function testConstruct(): void
    {
        $property = new Serialize(
            "name",
            true,
            true,
        );

        $this->assertSame("name", $property->getName());
        $this->assertSame(true, $property->isRequired());
        $this->assertSame(true, $property->allowsNull());
    }

    public function testConstructNoParams(): void
    {
        $property = new Serialize();

        $this->assertNull($property->getName());
        $this->assertNull($property->isRequired());
        $this->assertNull($property->allowsNull());
    }

    public function testGetAttribute(): void
    {
        $property = new ReflectionProperty($this, "name");
        $attribute = Serialize::getAttribute($property);
        $this->assertNotNull($attribute);
        $this->assertNull($attribute->getName());
        $this->assertNull($attribute->isRequired());
        $this->assertNull($attribute->allowsNull());
    }

    public function testGetAttributeOtherName(): void
    {
        $property = new ReflectionProperty($this, "otherName");
        $attribute = Serialize::getAttribute($property);
        $this->assertNotNull($attribute);
        $this->assertSame("other_name", $attribute->getName());
        $this->assertTrue($attribute->isRequired());
        $this->assertFalse($attribute->allowsNull());
    }

    public function testGetAttributeNone(): void
    {
        $property = new ReflectionProperty($this, "nonSerializedName");
        $attribute = Serialize::getAttribute($property);
        $this->assertNull($attribute);
    }

    public function testGetItemType(): void
    {
        $property = new Serialize(itemType: TestClass::class);
        $this->assertSame(TestClass::class, $property->getItemType());
    }
}