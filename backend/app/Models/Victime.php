<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Victime extends Model
{
    protected $fillable = [
        'incident_id', 'nom', 'prenom', 'age', 'sexe',
        'telephone', 'groupe_sanguin', 'etat', 'observations',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
