<?php

namespace App\config;

use JetBrains\PhpStorm\ArrayShape;

class Config
{
    /**
     * @return \string[][]
     */
    public static function config(): array
    {
        return [
            'db' => [
                'host' => 'mariadb',
                'port' => '3306',
                'dbname' => 'shop',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8',
            ],
        ];
    }
}