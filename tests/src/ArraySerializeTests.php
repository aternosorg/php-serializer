<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\BackedEnumSerializer;
use Aternos\Serializer\Serialize;

class ArraySerializeTests
{
    /** @noinspection PhpMissingFieldTypeInspection */
    #[Serialize(itemType: BuiltInTypeTestClass::class, itemSerializer: new ArraySerializer())]
    public $untypedArray = [];

    #[Serialize(itemSerializer: new ArraySerializer())]
    public array $array = [];

    #[Serialize(itemType: BuiltInTypeTestClass::class, itemSerializer: new ArraySerializer())]
    public array $typedArray = [];

    #[Serialize(itemSerializer: new BackedEnumSerializer())]
    protected array $backedEnumArray = [
        TestBackedEnum::A,
    ];

    #[Serialize]
    protected array $stringArray = [
        "",
    ];

    #[Serialize]
    protected array $intArray = [
        0,
    ];
}
