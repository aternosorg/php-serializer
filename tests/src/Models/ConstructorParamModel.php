<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Serialize;

class ConstructorParamModel
{
    protected string $param;
    protected string $optionalParam;
    #[Serialize]
    protected string $nonParam;

    public function __construct(
        #[Serialize]
        string           $param,
        #[Serialize]
        protected string $promoted,
        #[Serialize]
        string           $optionalParam = "1",
        #[Serialize]
        protected string $optionalPromoted = "2",
    )
    {
        $this->param = $param;
        $this->optionalParam = $optionalParam;
    }

    public function getParam(): string
    {
        return $this->param;
    }

    public function getPromoted(): string
    {
        return $this->promoted;
    }

    public function getOptionalParam(): string
    {
        return $this->optionalParam;
    }

    public function getOptionalPromoted(): string
    {
        return $this->optionalPromoted;
    }

    public function getNonParam(): string
    {
        return $this->nonParam;
    }
}
