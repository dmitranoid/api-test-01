<?php

namespace App\Models;

interface Model
{
    public static function fromArray($data):self;

    public function validate():mixed;
}