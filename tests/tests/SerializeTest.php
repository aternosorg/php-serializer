<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Aternos\Serializer\Test\Tests;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\Base64Deserializer;
use Aternos\Serializer\Test\Src\Base64Serializer;
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

    public function testGetCustomSerializerAndDeserializer(): void
    {
        $attribute = new Serialize(serializer: new Base64Serializer(), deserializer: new Base64Deserializer(TestClass::class));
        $this->assertNotNull($attribute);
        $this->assertInstanceOf(Base64Serializer::class, $attribute->getSerializer());
        $this->assertInstanceOf(Base64Deserializer::class, $attribute->getDeserializer());
    }

    public function testGetCustomItemSerializerAndDeserializer(): void
    {
        $attribute = new Serialize(itemSerializer: new Base64Serializer(), itemDeserializer: new Base64Deserializer(TestClass::class));
        $this->assertInstanceOf(Base64Serializer::class, $attribute->getItemSerializer());
        $this->assertInstanceOf(Base64Deserializer::class, $attribute->getItemDeserializer());
    }
}
