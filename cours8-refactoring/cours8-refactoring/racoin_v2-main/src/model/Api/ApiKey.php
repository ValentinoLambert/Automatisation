<?php

namespace model\Api;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'apikey';
    protected $primaryKey = 'id_apikey';
    public $timestamps = false;
}
