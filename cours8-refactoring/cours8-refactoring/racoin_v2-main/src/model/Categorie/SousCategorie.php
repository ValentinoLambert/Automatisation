<?php

namespace model\Categorie;

use Illuminate\Database\Eloquent\Model;

class SousCategorie extends Model
{
    protected $table = 'sous_categorie';
    protected $primaryKey = 'id_sous_categorie';
    public $timestamps = false;
}
