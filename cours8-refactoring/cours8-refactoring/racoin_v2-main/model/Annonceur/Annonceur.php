<?php

namespace model\Annonceur;

class Annonceur extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'annonceur';
    protected $primaryKey = 'id_annonceur';
    public $timestamps = false;

    public function annonce()
    {
        return $this->hasMany('model\Annonce\Annonce', 'id_annonceur');
    }
}
