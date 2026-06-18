<?php

namespace Aternos\Serializer\Test\Src\Models;

use Aternos\Serializer\Json\PropertyJsonSerializer;
use Aternos\Serializer\Serialize;
use Aternos\Serializer\Test\Src\Base64Deserializer;
use Aternos\Serializer\Test\Src\Base64Serializer;
use JsonSerializable;

class CustomSerializerModel implements JsonSerializable
{
    use PropertyJsonSerializer;

    #[Serialize(serializer: new Base64Serializer(), deserializer: new Base64Deserializer(SecondModel::class))]
    protected SecondModel $model;

    #[Serialize(itemSerializer: new Base64Serializer(), itemDeserializer: new Base64Deserializer(SecondModel::class))]
    protected array $testArray = [];

    protected int $propertyAfterTestArray = 0;

    public function __construct()
    {
        $this->model = new SecondModel();
        $this->testArray = [new SecondModel(), new SecondModel()];
    }

    /**
     * @return SecondModel
     */
    public function getModel(): SecondModel
    {
        return $this->model;
    }

    /**
     * @return SecondModel[]
     */
    public function getTestArray(): array
    {
        return $this->testArray;
    }

    /**
     * @param array $testArray
     * @return void
     */
    public function setTestArray(array $testArray): void
    {
        $this->testArray = $testArray;
    }
}
