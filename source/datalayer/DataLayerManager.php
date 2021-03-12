<?php


namespace Source\datalayer;


use PDOException;
use PDOStatement;

abstract class DataLayerManager
{
    /**
     * @var object|null
     */
    protected $data;

    /**
     * @var PDOException
     */
    protected $fail;
    /**
     * @var string|null
     */
    protected $message;

    /**
     * @return object|null
     */
    public function getData(): ?object
    {
        return $this->data;
    }

    /**
     * @return PDOException
     */
    public function getFail(): PDOException
    {
        return $this->fail;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    protected function create()
    {

    }

    protected function read(string $select, $bindValues): ?PDOStatement
    {
        try {
            $stmt = Connection::getInstance()->prepare($select);
            echo $select;

            if ($bindValues) {
                foreach ($bindValues as $k => $v) {
                    $stmt->bindValue($k, $v["value"], $v["type"]);
                }
            }

            var_dump($stmt->execute());
            return $stmt;
        }
        catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    protected function update()
    {

    }

    protected function delete()
    {

    }

    protected function safe(): ?array
    {
        return null;
    }

    protected function filter()
    {

    }
}