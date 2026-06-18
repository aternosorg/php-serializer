<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;

class DefaultValueModel
{
    #[Serialize(required: false)]
    public int $intWithDefault = 0;
    #[Serialize(required: false)]
    public int $intWithoutDefault;
    #[Serialize(required: false)]
    public ?int $nullableIntWithoutDefault;

    #[Serialize(required: false)]
    public string $stringWithDefault = "";
    #[Serialize(required: false)]
    public string $stringWithoutDefault;
    #[Serialize(required: false)]
    public ?string $nullableStringWithoutDefault;
}
