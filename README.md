# aternos/serializer

A PHP library for (de-)serialization using attributes and reflection.

## Installation

```bash
composer require aternos/serializer
```

## Usage

### Setup

Add the Serialize attribute the properties you want to serialize.
You can optionally specify the serialized name of the property, whether it is required, or whether it allows null values.
```php

class ExampleClass {
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
```

### Serialization

Now you can serialize and deserialize objects of this class:

```php
$example = new ExampleClass("John", 42, "Doe");
$serializer = new \Aternos\Serializer\ArraySerializer();
$serialized = $serializer->serialize($example);
// $serialized is now ["name" => "John", "age" => 42, "last_name" => "Doe"]
```

If you want to serialize directly to JSON, you can use the JsonSerializer:
```php
$example = new ExampleClass("John", 42, "Doe");
$jsonSerializer = new \Aternos\Serializer\JsonSerializer();
$serialized = $jsonSerializer->serializeToJson($example);
// $serialized is now '{"name":"John","age":42,"last_name":"Doe"}'
```

### Deserialization
To deserialize an object, you can use the `Deserializer` class:

```php
$data = ["name" => "John", "age" => 42, "last_name" => "Doe"];
$deserializer = new \Aternos\Serializer\ArrayDeserializer(ExampleClass::class);
$example = $deserializer->deserialize($data);
```

For deserializing JSON use the `JsonDeserializer`:
```php
$json = '{"name":"John","age":42,"last_name":"Doe"}';
$jsonDeserializer = new \Aternos\Serializer\JsonDeserializer(ExampleClass::class);
$example = $jsonDeserializer->deserialize($json);
```

### JsonSerializable

This library also provides a trait which implements the JsonSerializable interface:
```php
class ExampleClass implements \JsonSerializable {
    use \Aternos\Serializer\PropertyJsonSerializer;
    
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
```

Now you can use the `json_encode` function to serialize objects of this class:
```php
$example = new ExampleClass("John", 42, "Doe");
$serialized = json_encode($example);
// $serialized is now '{"name":"John","age":42,"last_name":"Doe"}'
```
