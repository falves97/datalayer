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
     * @var string $typeObject class do objeto
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

    /**
     * @return string
     */
    public function getTypeObject(): string
    {
        return $this->typeObject;
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

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return bool
     */
    public function isTimeStamp(): bool
    {
        return $this->timeStamp;
    }



    public function save($obj)
    {
        $id = null;

        if (!is_null($obj)) {

            $objEcxist = null;//false
            $method = new ReflectionMethod($this->getTypeObject(), "get" . ucfirst($this->primaryKey));

            //testa se a chave primária (id) do objeto passado já existe no banco
            try {
                //testa se o valor da chave priária no obj é null
                if (!is_null($method->invoke($obj, null)))
                {
                    //testa se ele já está no bd
                    $objEcxist = $this->find([$this->primaryKey => $method->invoke($obj, null)]);
                }
            } catch (ReflectionException $e) {
                $this->message = $e->getMessage();
                return null;
            }

            $col = implode(", ", array_values($this->getColuns()));
            $values = ":" . implode(", :", array_keys($this->getColuns()));

            $bindValues = [];

            foreach (array_keys($this->getColuns()) as $v) {
                $rfMethod = new ReflectionMethod($this->getTypeObject(), "get" . ucfirst($v));
                $value = $rfMethod->invoke($obj, null);

                $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                $bindValues[":{$v}"] = ["value" => $value, "type" => $type];
            }

            //se o id já existir ele faz um update, se não, cria o dado no bd
            if ($objEcxist) {
                try {
                    $rfMethod = new ReflectionMethod($this->getTypeObject(), "get" . ucfirst($this->primaryKey));

                    $value = $rfMethod->invoke($obj, null);
                    $this->update($obj, array_keys($this->getColuns()),[$this->primaryKey => $value]);
                } catch (ReflectionException $e) {
                    $this->message = $e->getMessage();
                }
            }
            else {
                $into = "INSERT INTO ". $this->getEntity() . " (" . $col .") VALUES (" . $values . ")";
                $id = $this->createOrUpdate($into, $bindValues);
            }
        }

        return $id;
    }

    public function update($obj, $params, $where)
    {
        if (!is_null($obj))
        {
            $bindValues = [];
            $whereQuery = "";
            $paramsQuery = "";

            foreach ($where as $pkey => $value) {
                $whereQuery = $whereQuery . self::getColuns()[$pkey] . " = :" . self::getColuns()[$pkey] . ", ";

                $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                $bindValues[":{$this->getColuns()[$pkey]}"] = ["value" => $value, "type" => $type];
            }

            foreach ($params as $pkey) {
                $paramsQuery = $paramsQuery . self::getColuns()[$pkey] . " = :" . self::getColuns()[$pkey] . ", ";

                $value = null;

                try {
                    $rfMethod = new ReflectionMethod($this->getTypeObject(), "get" . ucfirst($pkey));
                    $value = $rfMethod->invoke($obj, null);
                } catch (ReflectionException $e) {
                    $this->message = $e->getMessage();
                }

                $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                $bindValues[":{$this->getColuns()[$pkey]}"] = ["value" => $value, "type" => $type];
            }

            $whereQuery = substr($whereQuery, 0, -2); //retira a virgula e espaço no final
            $paramsQuery = substr($paramsQuery, 0, -2); //retira a virgula e espaço no final

            var_dump($whereQuery, $paramsQuery, $bindValues);

            $id = $this->createOrUpdate("UPDATE {$this->getEntity()} SET {$paramsQuery} WHERE {$whereQuery}", $bindValues);
            return $id;
        }

        return null;
    }

    /**
     * @param $params
     * @param string $colums
     * @return mixed|null
     */
    public function find($params, $colums = "*"): ?array
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
     * @param int $limit
     * @param int $offset
     * @param string $colums
     * @return array|null
     */
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

    public function destroy($params)
    {
        $bindValues = [];
        $where = "";
        $rowCount = null;

        if ($params) {
            foreach ($params as $pkey => $value) {
                $where = $where . self::getColuns()[$pkey] . " = :" . self::getColuns()[$pkey] . ", ";

                $key = self::getColuns()[$pkey];
                $type = (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                $bindValues[":{$key}"] = ["value" => $value, "type" => $type];
            }

            $where = substr($where, 0, -2); //retira a virgula e espaço no final


            $delete = "DELETE FROM {$this->getEntity()} WHERE {$where}";
            $rowCount = $this->delete($delete, $bindValues);
        }

        return $rowCount;
    }

    public function require()
    {

    }

    protected function safe($data): ?array
    {
        foreach ($this->safe as $item) {
            unset($data[$item]);
        }

        return $data;
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

    /**
     * @param array $datas
     * @return array|null
     * @throws ReflectionException
     */
    public function loadAll(array $datas): ?array
    {
        $objects = [];
        foreach ($datas as $key => $data) {
            $objects[$key] = $this->loadObject($data);
        }

        return $objects;
    }
}