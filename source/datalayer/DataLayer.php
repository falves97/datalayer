<?php


namespace Source\datalayer;


use PDO;

class DataLayer extends DataLayerManager
{
    /**
     * @var string $entity tabela a ser usada
     */
    private $entity;

    /**
     * @var array $entity colunas das tabela
     */
    private $coluns;

    /**
     * @var array $safe colunas que não devem ser alteradas on update or create
     */
    private $safe;

    /**
     * @var string
     */
    private string $primaryKey;

    /**
     * @var bool
     */
    private bool $timeStamp;

    /**
     * DataLayer constructor.
     * @param string $entity
     * @param array $coluns
     * @param array $safe
     * @param string $primaryKey
     * @param bool $timeStamp
     */
    public function __construct(string $entity, array $coluns, array $safe = [], string $primaryKey = "id", bool $timeStamp = false)
    {
        $this->entity = $entity;
        $this->coluns = $coluns;
        $this->safe = $safe;
        $this->primaryKey = $primaryKey;
        $this->timeStamp = $timeStamp;
    }


    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getColuns(): array
    {
        return $this->coluns;
    }

    /**
     * @return array
     */
    public function getSafe(): array
    {
        return $this->safe;
    }

    public function bootstrap() {

    }

    public function load($params, $columns = "*")
    {
        $bindValues = [];
        $where = "";

        foreach ($params as $pkey => $value)
        {
            $where = $where . self::getColuns()[$pkey] . " = :" . self::getColuns()[$pkey] . ", ";

            $key = self::getColuns()[$pkey];
            $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $bindValues[":{$key}"] = ["value" => $value, "type" => $type];
        }

        $where = substr($where, 0, -2); //retira a virgula e espaço no final

        $load = $this->read("SELECT " . $columns . " FROM " . self::getEntity() . " WHERE " . $where, $bindValues);

        return null;
    }

    public function find($id)
    {

    }

    public function all($limit = 30, $offset = 0) {

    }

    public function destroy()
    {

    }

    public function require()
    {

    }
}