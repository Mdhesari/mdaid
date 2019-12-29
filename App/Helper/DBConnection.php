<?php

namespace App\Helper;

use PDO;

class DBConnection
{

    private $config = [];

    private static $instance = null;

    private $pdo;

    private function __construct()
    {

        $this->setConfig();

        $database = $this->config['connection']['mysql'];

        $dsn = "mysql:host={$database['host']};dbname={$database['database']};charset={$database['charset']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {

            $this->pdo = new PDO($dsn, $database['user'], $database['password'], $options);
            // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        } catch (\PDOException $error) {

            dd("Database Error Occured : " . $error->getMessage());
        }
    }

    public function getConnection()
    {

        return $this->pdo;
    }

    public static function Singleton()
    {

        if (is_null(self::$instance)) {

            self::$instance = new DBConnection;
        }

        return self::$instance;
    }

    private function setConfig()
    {
        $this->config = include CONFIG_PATH;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    public function __clone()
    { }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    { }
}
