# aternos/serializer

A PHP library for (de-)serialization using attributes and reflection.

## Installation

```bash
composer require aternos/serializer
```

## Usage

Add the SerializationProperty attribute the properties you want to serialize.
You can optionally specify the serialized name of the property, whether it is required, or whether it allows null values.
```php

class ExampleClass {
    public function __construct(
        #[SerializationProperty]
        protected string $name,
        #[SerializationProperty(required: false)]
        protected int $age = 0,
        #[SerializationProperty(name: "last_name")]
        protected ?string $lastName = null
    ) {
    
    }
}
```

Now you can serialize and deserialize objects of this class:
```php
$example = new ExampleClass("John", 42, "Doe");
$serializer = new Serializer();
$serialized = $serializer->serialize($example);
// $serialized is now ["name" => "John", "age" => 42, "last_name" => "Doe"]
$deserialized = $serializer->deserialize(ExampleClass::class, $serialized);
// $deserialized is now an instance of ExampleClass with the values from $serialized
```

If you want to serialize directly to JSON, you can use the JsonSerializer:
```php
$example = new ExampleClass("John", 42, "Doe");
$jsonSerializer = new JsonSerializer();
$serialized = $jsonSerializer->serializeToJson($example);
// $serialized is now '{"name":"John","age":42,"last_name":"Doe"}'
$deserialized = $jsonSerializer->deserialize(ExampleClass::class, $serialized);
// $deserialized is now an instance of ExampleClass with the values from $serialized
```

This library also provides a trait which implements the JsonSerializable interface:
```php
class ExampleClass implements JsonSerializable {
    use JsonSerializableTrait;
    
    public function __construct(
        #[SerializationProperty]
        protected string $name,
        #[SerializationProperty(required: false)]
        protected int $age = 0,
        #[SerializationProperty(name: "last_name")]
        protected ?string $lastName = null
    ) {
    
    }
}
```

Now you can use the `json_encode` function to serialize objects of this class:
```php
$example = new ExampleClass("John", 42, "Doe");
$serialized = json_encode($example);
// $serialized is now '{"name":"John","age":42,"last_name":"Doe"}'
```
