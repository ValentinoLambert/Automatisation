<?php

namespace model\Annonceur;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annonceur extends Model
{
    protected $table = 'annonceur';
    protected $primaryKey = 'id_annonceur';
    public $timestamps = false;

    public function annonce(): HasMany
    {
        return $this->hasMany('model\Annonce\Annonce', 'id_annonceur');
    }
}
