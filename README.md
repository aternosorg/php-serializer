# aternos/serializer

A PHP library for (de-)serialization using attributes and reflection.

- [Installation](#installation)
- [Usage](#usage)
- [The Serialize Attribute](#the-serialize-attribute)
  - [Name](#name)
  - [Required](#required)
  - [Allow Null](#allow-null)
  - [Item Type](#item-type)
- [Exceptions](#exceptions)
  - [SerializationException](#serializationexception)
  - [InvalidInputException](#invalidinputexception)
  - [MissingPropertyException](#missingpropertyexception)
  - [IncorrectTypeException](#incorrecttypeexception)
  - [UnsupportedTypeException](#unsupportedtypeexception)
- [Custom Serializers](#custom-serializers)

### Installation

```bash
composer require aternos/serializer
```

### Usage

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

> [!NOTE]
> Deserialization is not supported for intersection types as there is no way to determine the correct type.

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
The `Serialize` attribute can be used to configure the serialization of a property.
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
#[Serialize]
protected string $name;
// ^ required

#[Serialize]
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
#[Serialize]
protected string $a;
// ^ does not allow null

#[Serialize(allowNull: false)]
protected ?string $c = null;
// ^ does not allow null

#[Serialize]
protected ?string $b;
// ^ allows null

#[Serialize(allowNull: true)]
protected string $c;
// ^ allows null
// If not set in the serialized data, the property is not initialized
// Accessing it before initialization will result in a fatal PHP error
```

#### Item Type
This option can be used to specify the type of the items in an array. It allows the deserializer to convert
objects in the array to the correct type. If this is not set, items will not be converted to any type.

Array keys are preserved during the conversion.

```php
#[Serialize(itemType: ExampleClass::class)]
protected array $examples;
// ^ items in the array will be converted to ExampleClass

#[Serialize]
protected array $otherExamples;
// ^ items in the array will not be converted
```

#### Serializer and Deserializer
A custom Serializer and Deserializer can be specified for a property.
This can be useful if you want to serialize a specific property in a different way.

```php
#[Serialize(serializer: new Base64Serializer(), deserializer: new Base64Deserializer(TestClass::class))]
protected TestClass $example;
```

Note that the custom Deserializer is responsible for returning the correct type.
If an incompatible type is returned, an IncorrectTypeException is thrown.

#### Item Serializer and Item Deserializer

Custom Serializers and Deserializers can also be specified for array items.

```php
#[Serialize(itemSerializer: new Base64Serializer(), itemDeserializer: new Base64Deserializer(TestClass::class))]
protected array $example = [];
```

### Exceptions
The following exceptions may be thrown during serialization or deserialization:
- [MissingPropertyException](#missingpropertyexception)
- [IncorrectTypeException](#incorrecttypeexception)

Both of these exceptions extend [InvalidInputException](#invalidinputexception).

During deserialization, the following additional exceptions may be thrown:
- [UnsupportedTypeException](#unsupportedtypeexception)
- [JsonException](https://www.php.net/manual/en/class.jsonexception.php)

JsonException is a built-in PHP exception that is thrown when an error occurs during JSON encoding or decoding.
All other exceptions extend [SerializationException](#serializationexception) and are described below.

#### SerializationException
This is a common parent class for all exceptions thrown during serialization or deserialization (except the PHP-built-in JsonException).
It is useful for catching, but never instantiated directly.

#### InvalidInputException
This is a parent class for all exceptions caused by an invalid input.
During serialization, the input in question is the PHP object you want to serialize.
For deserialization, the input refers to the JSON data.

#### MissingPropertyException
During serialization this is thrown if a required property is not initialized.
During deserialization this is thrown if a required property is missing in the input data.

#### IncorrectTypeException
During serialization this is thrown if a property has a null value, but does not [allow null](#allow-null).
During deserialization this is thrown if a property has a value of an incorrect type
(e.g. an int is passed for a string property).

#### UnsupportedTypeException
As noted above, deserializing intersection types is not supported.
If an intersection type is encountered during deserialization, this exception is thrown.
It's also thrown if a php built-in type is encountered that is not yet supported by the library.

### Custom Serializers
If you want to write a serializer for a different format, you can use the ArraySerializer and ArrayDeserializer class.
These convert the object to an associative array and vice versa.

```php
$example = new ExampleClass("John", 25, "Doe");
$serializer = new \Aternos\Serializer\Array\ArraySerializer();
$deserializer = new \Aternos\Serializer\Array\ArrayDeserializer(ExampleClass::class);
try {
    $array = $serializer->serialize($example);
    // ^ ['name' => 'John', 'age' => 25, 'last_name' => 'Doe']
    $example = $deserializer->deserialize($array);
} catch (SerializationException $e) {
    // handle exception
}
```
See the JsonSerializer and JsonDeserializer classes for an example implementation.
