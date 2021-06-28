<?php


namespace App\core;


abstract class Controller
{
    private $model = [];

    function render($name, $type = 'html')
    {
        (new View($name, $this->model, $type))->render();
    }

    function addModelAttribute($key, $value)
    {
        $this->model[$key] = $value;
    }

}