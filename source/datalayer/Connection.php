<?php


namespace Source\datalayer;

use PDO;
use PDOException;

class Connection
{
    private static $instance;

    final private function __construct()
    {
    }

    final private function __clone(): void
    {
    }


    public static function getInstance(): PDO
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new PDO(
                    DATA_LAYER_CONFIG["driver"] . ":host=" . DATA_LAYER_CONFIG["host"] . ";dbname" . DATA_LAYER_CONFIG["dabname"],
                    DATA_LAYER_CONFIG["user"],
                    DATA_LAYER_CONFIG["passwd"],
                    DATA_LAYER_CONFIG["options"]
                );

            }
            catch (PDOException $exception) {
                die("Erro ao se conectar ao banco de dados");
            }
        }

        return self::$instance;
    }
}