<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\TestBackedEnum;

class BackedEnumModel
{
    #[Serialize]
    protected TestBackedEnum $enum = TestBackedEnum::A;

    public function getEnum(): TestBackedEnum
    {
        return $this->enum;
    }
}
