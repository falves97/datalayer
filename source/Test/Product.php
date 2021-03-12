<?php


namespace Source\Test;

use Source\datalayer\DataLayer;

class Product extends DataLayer
{
    private $id;
    private $name;
    private $description;
    private $value;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "products",
            ["id" => "id", "name" => "name", "descripition" => "description", "value" => "value"]
        );
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() :string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}