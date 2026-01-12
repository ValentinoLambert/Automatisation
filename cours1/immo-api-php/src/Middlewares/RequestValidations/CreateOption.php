<?php

namespace App\Middlewares\RequestValidations;

use Respect\Validation\Validator;
use App\Middlewares\RequestValidations\RequestValidation;

class CreateOption extends RequestValidation
{
    public array $rules;

    public function __construct()
    {
        $this->rules =
        [
          'name' => Validator::length(5, 255),
        ];
        parent::__construct($this->rules, 'body');
    }
}
