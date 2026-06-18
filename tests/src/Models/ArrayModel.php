<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\ArraySerializer;
use Aternos\Serializer\BackedEnumSerializer;
use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\TestBackedEnum;

class ArrayModel
{
    /** @noinspection PhpMissingFieldTypeInspection */
    #[Serialize(itemType: BuiltInTypeModel::class, itemSerializer: new ArraySerializer())]
    public $untypedArray = [];

    #[Serialize(itemSerializer: new ArraySerializer())]
    public array $array = [];

    #[Serialize(itemType: BuiltInTypeModel::class, itemSerializer: new ArraySerializer())]
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
