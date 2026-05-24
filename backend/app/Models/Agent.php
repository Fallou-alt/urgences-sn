<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = ['identifiant', 'mot_de_passe', 'nom', 'prenom', 'role', 'actif', 'token'];

    protected $hidden = ['mot_de_passe', 'token'];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
