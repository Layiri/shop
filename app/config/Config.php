<?php

namespace App\config;

use JetBrains\PhpStorm\ArrayShape;

class Config
{
    #[ArrayShape(['db' => "string[]"])]
    public static function config(): array
    {
        return [
            'db' => [
                'host' => 'localhost',
                'port' => '3308',
                'dbname' => 'gog',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
            ],
        ];
    }
}