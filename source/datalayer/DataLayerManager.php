<?php


namespace Source\datalayer;

use PDOException;
use PDOStatement;

abstract class DataLayerManager
{

    /**
     * @var PDOException
     */
    protected $fail;
    /**
     * @var string|null
     */
    protected $message;


    /**
     * @return PDOException
     */
    public function getFail(): ?PDOException
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

    protected function createOrUpdate(string $into, array $bindValues): ?int
    {
        try {
            $stmt = Connection::getInstance()->prepare($into);
            if ($bindValues) {
                foreach ($bindValues as $k => $v) {
                    $stmt->bindValue($k, $v["value"], $v["type"]);
                }
            }

            $stmt->execute();
            return Connection::getInstance()->lastInsertId();
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    protected function read(string $select, array $bindValues): ?PDOStatement
    {
        try {
            $stmt = Connection::getInstance()->prepare($select);
            if ($bindValues) {
                foreach ($bindValues as $k => $v) {
                    $stmt->bindValue($k, $v["value"], $v["type"]);
                }
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    protected function delete($delete, $bindValues): ?int
    {
        try {
            $stmt = Connection::getInstance()->prepare($delete);
            if ($bindValues) {
                foreach ($bindValues as $k => $v) {
                    $stmt->bindValue($k, $v["value"], $v["type"]);
                }
            }

            if ($bindValues) {
                foreach ($bindValues as $k => $v) {
                    $stmt->bindValue($k, $v["value"], $v["type"]);
                }
            }

            var_dump($delete, $bindValues);


            $stmt->execute();
            return ($stmt->rowCount() ?? 1);
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }


    /**
     * @param array $data
     * @return array|null
     */
    protected function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null: filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
        }

        return $filter;
    }
}