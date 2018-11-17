<?php

namespace App\Models;


use Illuminate\Contracts\Container\Container;

class Game
{
    private $container;
    private $field;

    public function __construct(Container $container)
    {
        $this->container = $container;
        // TODO: получить игровое поле из сессии
        echo 'Session field: ' . (string) session()->has('field');
        if (session()->has('field')) {
            $this->field = session('field');
        } else {
            $this->field = $container->make(Field::class);
            $this->field->reset();
        }
    }

    public function getField() : Field
    {
        return $this->field;
    }

    public function save() : void
    {
        session([
            'field' => $this->field,
        ]);
        session()->save();
    }
}