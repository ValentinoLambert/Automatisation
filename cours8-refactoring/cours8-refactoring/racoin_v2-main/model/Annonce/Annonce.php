<?php

namespace model\Annonce;

class Annonce extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'annonce';
    protected $primaryKey = 'id_annonce';
    public $timestamps = false;
    public $links = null;


    public function annonceur()
    {
        return $this->belongsTo('model\Annonceur\Annonceur', 'id_annonceur');
    }

    public function photo()
    {
        return $this->hasMany('model\Annonce\Photo', 'id_photo');
    }

}
?>
