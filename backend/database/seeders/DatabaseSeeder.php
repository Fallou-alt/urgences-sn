<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Incident;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            ['identifiant' => 'admin',    'mot_de_passe' => Hash::make('admin123'),   'nom' => 'Diallo',  'prenom' => 'Moussa',   'role' => 'admin'],
            ['identifiant' => 'pompier1', 'mot_de_passe' => Hash::make('pompier123'), 'nom' => 'Ndiaye',  'prenom' => 'Ibrahima', 'role' => 'pompier'],
            ['identifiant' => 'pompier2', 'mot_de_passe' => Hash::make('pompier123'), 'nom' => 'Sow',     'prenom' => 'Oumar',    'role' => 'pompier'],
            ['identifiant' => 'samu1',    'mot_de_passe' => Hash::make('samu123'),    'nom' => 'Fall',    'prenom' => 'Fatou',    'role' => 'samu'],
            ['identifiant' => 'samu2',    'mot_de_passe' => Hash::make('samu123'),    'nom' => 'Ba',      'prenom' => 'Aminata',  'role' => 'samu'],
        ];

        foreach ($agents as $a) {
            Agent::firstOrCreate(['identifiant' => $a['identifiant']], $a);
        }

        $incidents = [
            ['type_urgence' => 'incendie', 'latitude' => 14.6937, 'longitude' => -17.4441, 'adresse' => 'Plateau, Dakar',        'description' => 'Incendie dans un immeuble',     'statut' => 'en_attente'],
            ['type_urgence' => 'medical',  'latitude' => 14.7167, 'longitude' => -17.4677, 'adresse' => 'Parcelles Assainies',    'description' => 'Malaise cardiaque',             'statut' => 'en_route'],
            ['type_urgence' => 'accident', 'latitude' => 14.7500, 'longitude' => -17.3500, 'adresse' => 'Route de Rufisque',      'description' => 'Accident de la route',          'statut' => 'sur_place'],
            ['type_urgence' => 'medical',  'latitude' => 14.6800, 'longitude' => -17.4300, 'adresse' => 'Médina, Dakar',          'description' => 'Femme enceinte en détresse',    'statut' => 'en_attente'],
            ['type_urgence' => 'autre',    'latitude' => 14.7300, 'longitude' => -17.4600, 'adresse' => 'Guédiawaye',             'description' => 'Inondation quartier résidentiel','statut' => 'termine'],
        ];

        foreach ($incidents as $i) {
            Incident::create($i);
        }
    }
}
