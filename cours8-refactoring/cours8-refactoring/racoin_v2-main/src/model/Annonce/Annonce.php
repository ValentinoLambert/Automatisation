<?php

namespace model\Annonce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annonce extends Model
{
    protected $table = 'annonce';
    protected $primaryKey = 'id_annonce';
    public $timestamps = false;
    public ?string $links = null;

    public function annonceur(): BelongsTo
    {
        return $this->belongsTo('model\Annonceur\Annonceur', 'id_annonceur');
    }

    public function photo(): HasMany
    {
        return $this->hasMany('model\Annonce\Photo', 'id_annonce');
    }

    /**
     * Retourne l'URL de la photo principale ou une image par défaut.
     */
    public function getMainPhotoUrl(string $basePath = ''): string
    {
        $photo = $this->photo()->first();
        if ($photo) {
            return $photo->url_photo;
        }
        return $basePath . '/img/noimg.png';
    }
}
