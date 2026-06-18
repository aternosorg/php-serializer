<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\Base64Deserializer;
use Aternos\Serializer\Test\Src\Base64Serializer;
use JsonSerializable;

class CustomSerializerInvalidTypeModel implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize(serializer: new Base64Serializer(), deserializer: new Base64Deserializer(SecondModel::class))]
    protected FirstModel $model;

    #[Serialize(itemSerializer: new Base64Serializer(), itemDeserializer: new Base64Deserializer(SecondModel::class))]
    protected array $testArray = [];

    public function __construct()
    {
        $this->model = new FirstModel();
    }
}
