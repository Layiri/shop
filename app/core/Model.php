<?php


namespace App\core;

use App\config\Config;
use PDO;
use PDOException;

abstract class Model
{
    public abstract static function table(): string;

    protected static PDO $conn;

    function __construct()
    {
        $config = Config::config();
        try {
            self::$conn = new PDO("mysql:host=" . $config['db']['host'] . ";port=" . $config['db']['port'] . ";dbname=" . $config['db']['dbname'], $config['db']['username'], $config['db']['password']);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "<br>" . $e->getMessage();
        }
    }

}