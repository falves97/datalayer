<?php


namespace Source\Test;

class Product
{
    private $meuId;
    private $name;
    private $description;
    private $value;

    /**
     * Product constructor.
     */
    public function __construct()
    {
    }


    /**
     * @return int
     */
    public function getMeuId(): ?int
    {
        return $this->meuId;
    }

    /**
     * @param mixed $meuId
     */
    public function setMeuId($meuId): void
    {
        $this->meuId = $meuId;
    }



    /**
     * @return string
     */
    public function getName(): ?string
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
    public function getDescription(): ?string
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
    public function getValue(): ?float
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