<?php

namespace App\Middlewares\RequestValidations;

use Respect\Validation\Validator;
use App\Middlewares\RequestValidations\RequestValidation;

class ListProperty extends RequestValidation
{
    public array $rules;

    public function __construct()
    {
        $this->rules =
        [
          'name' => Validator::optional(Validator::stringVal()),
          'types' => Validator::optional(Validator::stringVal()),
          'price_gt' => Validator::optional(Validator::positive()),
          'price_lt' => Validator::optional(Validator::positive()),
          'sold' => Validator::optional(Validator::boolVal()),
        ];
        parent::__construct($this->rules, 'query');
    }
}
