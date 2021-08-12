<?php

class Db
{
    private static $_instance = null;

    private function __construct () {

        try {
            self::$_instance = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASSWORD,
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
            );
        } catch (Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * @return Db|null|PDO
     */
    public static function getInstance()
    {
        if (self::$_instance != null) {
            return self::$_instance;
        }

        return new self();
    }

    /**
     * @param $query
     * @param array $data
     * @return bool|PDOStatement
     */
    public function query($query, $data = [])
    {
        try {
            $stmt = self::$_instance->prepare(
                $query
            );
            $stmt->execute($data);
        } catch (Exception $e) {
            echo $e;
            exit;
        }

        return $stmt;
    }

    public function lastInsertId($name = null)
    {
        return self::$_instance->lastInsertId($name);
    }
}