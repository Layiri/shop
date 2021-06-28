<?php


namespace App\core;


interface IModel
{
    public function all();

    public function one();

    public function save(): bool;

    public function update(): bool;

    public function delete(): bool;

}