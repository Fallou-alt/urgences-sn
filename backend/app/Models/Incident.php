<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'type_urgence', 'latitude', 'longitude', 'adresse',
        'description', 'nom_citoyen', 'telephone_citoyen', 'statut', 'agent_id'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function victimes()
    {
        return $this->hasMany(Victime::class);
    }
}
