<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $fillable = [
        'nom', 'sigle', 'type', 'region', 'adresse', 'telephone', 'email',
        'responsable_nom', 'responsable_titre', 'actif'
    ];

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }
}
