<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class BackedEnumTestClass
{
    #[Serialize]
    protected TestBackedEnum $enum = TestBackedEnum::A;

    public function getEnum(): TestBackedEnum
    {
        return $this->enum;
    }
}
