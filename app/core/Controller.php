<?php


namespace App\core;


abstract class Controller
{
    private $model = [];

    function render($name, $type = 'html')
    {
        (new View($name, $this->model, $type))->render();
    }

    /**
     * @param array $message
     */
    function renderJson(array $message)
    {
        echo json_encode($message,JSON_UNESCAPED_UNICODE);
        exit;
    }

    function addModelAttribute($key, $value)
    {
        $this->model[$key] = $value;
    }

}