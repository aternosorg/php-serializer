<?php

namespace Aternos\Serializer\Test\Src;

use Aternos\Serializer\Serialize;

class BuiltInTypeTestClass
{
    #[Serialize]
    public null|int $int = null;
    #[Serialize]
    public null|float $float = null;
    #[Serialize]
    public null|string $string = null;
    #[Serialize]
    public null|array $array = null;
    #[Serialize]
    public object|null $object = null;
    #[Serialize]
    public self|null $self = null;
    #[Serialize]
    public null|false $false = null;
    #[Serialize]
    public null|true $true = null;
}