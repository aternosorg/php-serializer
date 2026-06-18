<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\TestEnum;

class EnumModel
{
    #[Serialize]
    protected TestEnum $enum = TestEnum::A;

    public function getEnum(): TestEnum
    {
        return $this->enum;
    }
}
