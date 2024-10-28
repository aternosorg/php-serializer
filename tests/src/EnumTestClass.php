<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class EnumTestClass
{
    #[Serialize]
    protected TestEnum $enum = TestEnum::A;

    public function getEnum(): TestEnum
    {
        return $this->enum;
    }
}
