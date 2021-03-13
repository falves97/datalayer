<?php


namespace Source\datalayer;

use PDO;
use PDOException;

class Connection
{
    private static $instance;

    private function __construct()
    {
    }

    private function __clone(): void
    {
    }


    public static function getInstance(): PDO
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new PDO(
                    DATA_LAYER_CONFIG["driver"] . ":host=" . DATA_LAYER_CONFIG["host"] . ";dbname=" . DATA_LAYER_CONFIG["dbname"],
                    DATA_LAYER_CONFIG["username"],
                    DATA_LAYER_CONFIG["passwd"],
                    DATA_LAYER_CONFIG["options"]
                );
            }
            catch (PDOException $exception) {
                echo($exception->getMessage());
            }
        }

        return self::$instance;
    }
}
