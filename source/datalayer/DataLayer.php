<?php


namespace Source\datalayer;


use PDO;
use ReflectionException;
use ReflectionMethod;

class DataLayer extends DataLayerManager
{
    /**
     * @var string $entity tabela a ser usada
     */
    private string $entity;

    /**
     * @var string class do objeto
     */
    private string $typeObject;

    /**
     * @var array $entity colunas das tabela
     */
    private array $coluns;

    /**
     * @var array $safe colunas que não devem ser alteradas on update or create
     */
    private array $safe;

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
     * @param string $typeObject
     * @param array $coluns
     * @param array $safe
     * @param string $primaryKey
     * @param bool $timeStamp
     */
    public function __construct(string $entity, string $typeObject, array $coluns, array $safe, string $primaryKey = "id", bool $timeStamp = false)
    {
        $this->entity = $entity;
        $this->typeObject = $typeObject;
        $this->coluns = $coluns;
        $this->safe = $safe;
        $this->primaryKey = $primaryKey;
        $this->timeStamp = $timeStamp;
    }


    /**
     * @return array
     */
    public function getSafe(): array
    {
        return $this->safe;
    }

    public function bootstrap()
    {

    }

    /**
     * @param $params
     * @param string $colums
     * @return mixed|null
     */
    public function findAll($params, $colums = "*"): ?array
    {
        $bindValues = [];
        $where = "";

        foreach ($params as $pkey => $value) {
            $where = $where . self::getColuns()[$pkey] . " = :" . self::getColuns()[$pkey] . ", ";

            $key = self::getColuns()[$pkey];
            $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $bindValues[":{$key}"] = ["value" => $value, "type" => $type];
        }

        $where = substr($where, 0, -2); //retira a virgula e espaço no final
        $load = $this->read("SELECT " . $colums . " FROM " . self::getEntity() . " WHERE " . $where, $bindValues);

        if ($this->getFail() || !$load->rowCount()) {
            $this->message = "Erro ao carregar dados";
            return null;
        }

        return $load->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getColuns(): array
    {
        return $this->coluns;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    public function find($params, $colums = "*")
    {
        $bindValues = [];
        $where = "";

        foreach ($params as $pkey => $value) {
            $where = $where . self::getColuns()[$pkey] . " = :" . self::getColuns()[$pkey] . ", ";

            $key = self::getColuns()[$pkey];
            $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $bindValues[":{$key}"] = ["value" => $value, "type" => $type];
        }

        $where = substr($where, 0, -2); //retira a virgula e espaço no final
        $load = $this->read("SELECT " . $colums . " FROM " . self::getEntity() . " WHERE " . $where, $bindValues);

        if ($this->getFail() || !$load->rowCount()) {
            $this->message = "Erro ao carregar dados";
            return null;
        }

        return $load->fetch(PDO::FETCH_ASSOC);
    }

    public function all($limit = 30, $offset = 0, $colums = "*"): ?array
    {
        $bindValues = [];

        $bindValues[":l"] = ["value" => $limit, "type" => PDO::PARAM_INT];
        $bindValues[":o"] = ["value" => $offset, "type" => PDO::PARAM_INT];

        $load = $this->read("SELECT " . $colums . " FROM " . self::getEntity() . " LIMIT :l OFFSET :o", $bindValues);

        if ($this->getFail() || !$load->rowCount()) {
            $this->message = "Erro ao carregar dados";
            return null;
        }

        return $load->fetchAll(PDO::FETCH_ASSOC);
    }

    public function destroy()
    {

    }

    public function require()
    {

    }

    /**
     * @param array $data Os valores no bd
     * @return object|null
     * @throws ReflectionException
     */
    public function loadObject(array $data): ?object
    {
        $safeData = $this->safe($data);
        $obj = new ($this->getTypeObject())();

        //troca, chave por valor
        $colToAtr = array_flip($this->getColuns());

        foreach ($safeData as $key => $value) {

            $nameMethod = "set" . ucfirst($colToAtr[$key]);

            try {
                $rflMethod = new ReflectionMethod($this->getTypeObject(), $nameMethod);
            } catch (ReflectionException $e) {
                $this->message = $e->getMessage();
                return null;
            }

            $rflMethod->invoke($obj, $value);
        }

        return $obj;
    }

    public function loadAll(array $datas): ?array
    {
        $objects = [];
        foreach ($datas as $key => $data) {
            $objects[$key] = $this->loadObject($data);
        }

        return $objects;
    }

    protected function safe($data): ?array
    {
        foreach ($this->safe as $item) {
            unset($data[$item]);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getTypeObject(): string
    {
        return $this->typeObject;
    }
}