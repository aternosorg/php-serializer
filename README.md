# aternos/serializer

A PHP library for (de-)serialization using attributes and reflection.

## Installation

```bash
composer require aternos/serializer
```

## Usage

This library adds a simple trait which implements `\JsonSerializable` by serializing all properties
with the `#[Serialize]` attribute. This attribute can be used to configure the serialization of the property.

```php
use Aternos\Serializer\Serialize;
use Aternos\Serializer\Json\PropertyJsonSerializer;

class ExampleClass implements \JsonSerializable {
    use PropertyJsonSerializer;

    public function __construct(
        #[Serialize]
        protected string $name,
        #[Serialize(required: false)]
        protected int $age = 0,
        #[Serialize(name: "last_name")]
        protected ?string $lastName = null
    ) {
    
    }
}

$example = new ExampleClass("John", 25, "Doe");

try {
    $json = json_encode($example)
} catch (MissingPropertyException|IncorrectTypeException $e) {
    // handle exception
}
```
See [Exceptions](#exceptions) for more information on error handling.

The trait also adds static `fromJson` and `tryFromJson` methods for deserialization.

```php
$example = ExampleClass::fromJson('{ "name": "John", "age": 25, "last_name": "Doe" }');
// ^ throws an exception if the input is invalid (e.g. required property is missing)

$example = ExampleClass::tryFromJson('{ "name": "John", "age": 25, "last_name": "Doe" }');
// ^ returns null if the input is invalid
```

If you prefer you can also serialize and deserialize manually.

```php
$example = new ExampleClass("John", 25, "Doe");
$serializer = new \Aternos\Serializer\Json\JsonSerializer();
try {
    $json = $serializer->serialize($example);
} catch (MissingPropertyException|IncorrectTypeException $e) {
    // handle exception
}

$deserializer = new \Aternos\Serializer\Json\JsonDeserializer(ExampleClass::class);
try {
    $example = $deserializer->deserialize($json);
} catch (SerializationException|JsonException $e) {
    // handle exception
}
```

### The Serialize Attribute
The serialize attribute can be used to configure the serialization of a property.
It has the following options:

#### Name
This option can be used to change the name of the property in the serialized data.
If not specified, the property name is used.

This is useful if the serialized data uses a different naming convention (e.g. snake-case).

```php
#[Serialize(name: "last_name")]
protected ?string $lastName = null;
```

It also allows serializing properties with identifiers that are not allowed in PHP.
```php
#[Serialize(name: "0")]
protected string $zero = "Example";
```

#### Required
This option can be used to specify whether the property is required in the serialized data.
If this is not set, the property is not required if it has a default value.

```php
#[Serialize()]
protected string $name;
// ^ required

#[Serialize()]
protected int $age = 1;
// ^ not required
// If not set in the serialized data, the default value (1) is used

#[Serialize(required: true)]
protected string $required = "default";
// ^ required

#[Serialize(required: false)]
protected string $notRequired;
// ^ not required
// If not set in the serialized data, the property is not initialized
// Accessing it before initialization will result in a fatal PHP error
```

#### Allow Null
This option can be used to specify whether the property can be `null` in the serialized data.
If this is not set, and the property is annotated with a type the nullability of that type is used.
If no type is provided, the property is allowed to be `null`.

```php
#[Serialize()]
protected string $a;
// ^ does not allow null

#[Serialize(allowNull: false)]
protected ?string $c = null;
// ^ does not allow null

#[Serialize()]
protected ?string $b;
// ^ allows null

#[Serialize(allowNull: true)]
protected string $c;
// ^ allows null
// If not set in the serialized data, the property is not initialized
// Accessing it before initialization will result in a fatal PHP error
```

### Exceptions
TODO

### Custom Serializers
TODO
