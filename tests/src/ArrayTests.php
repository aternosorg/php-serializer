<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class ArrayTests
{
    /** @noinspection PhpMissingFieldTypeInspection */
    #[Serialize(itemType: BuiltInTypeTestClass::class)]
    public $untypedArray = [];

    #[Serialize]
    public array $array = [];

    #[Serialize(itemType: BuiltInTypeTestClass::class)]
    public array $typedArray = [];
}