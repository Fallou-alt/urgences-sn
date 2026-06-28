<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Incident;
use App\Models\Structure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Structures de secours
        $structures = [
            ['nom' => 'Sapeurs-Pompiers de Dakar',       'sigle' => 'SPD',  'type' => 'pompiers',         'region' => 'Dakar'],
            ['nom' => 'SAMU National',                   'sigle' => 'SAMU', 'type' => 'samu',             'region' => 'Dakar'],
            ['nom' => 'Police Nationale',                'sigle' => 'PN',   'type' => 'police',           'region' => 'Dakar'],
            ['nom' => 'Gendarmerie Nationale',           'sigle' => 'GN',   'type' => 'gendarmerie',      'region' => 'Dakar'],
            ['nom' => 'Marine Nationale',                'sigle' => 'MN',   'type' => 'marine',           'region' => 'Dakar'],
            ['nom' => 'Protection Civile',               'sigle' => 'PC',   'type' => 'protection_civile','region' => 'Dakar'],
        ];
        foreach ($structures as $s) {
            Structure::firstOrCreate(['sigle' => $s['sigle']], $s);
        }

        $pompiers = Structure::where('type','pompiers')->first();
        $samu     = Structure::where('type','samu')->first();

        $agents = [
            ['identifiant' => 'admin',    'mot_de_passe' => Hash::make('admin123'),   'nom' => '',  'prenom' => '',   'role' => 'admin',   'structure_id' => null],
            ['identifiant' => 'pompier1', 'mot_de_passe' => Hash::make('pompier123'), 'nom' => '',  'prenom' => '',   'role' => 'pompier', 'structure_id' => $pompiers?->id],
            ['identifiant' => 'pompier2', 'mot_de_passe' => Hash::make('pompier123'), 'nom' => '',  'prenom' => '',   'role' => 'pompier', 'structure_id' => $pompiers?->id],
            ['identifiant' => 'samu1',    'mot_de_passe' => Hash::make('samu123'),    'nom' => '',  'prenom' => '',   'role' => 'samu',    'structure_id' => $samu?->id],
            ['identifiant' => 'samu2',    'mot_de_passe' => Hash::make('samu123'),    'nom' => '',  'prenom' => '',   'role' => 'samu',    'structure_id' => $samu?->id],
        ];
        foreach ($agents as $a) {
            Agent::firstOrCreate(['identifiant' => $a['identifiant']], $a);
        }

        $incidents = [
            ['type_urgence' => 'incendie', 'latitude' => 14.6937, 'longitude' => -17.4441, 'adresse' => 'Plateau, Dakar',        'description' => 'Incendie dans un immeuble',      'statut' => 'en_attente'],
            ['type_urgence' => 'medical',  'latitude' => 14.7167, 'longitude' => -17.4677, 'adresse' => 'Parcelles Assainies',    'description' => 'Malaise cardiaque',              'statut' => 'en_route'],
            ['type_urgence' => 'accident', 'latitude' => 14.7500, 'longitude' => -17.3500, 'adresse' => 'Route de Rufisque',      'description' => 'Accident de la route',           'statut' => 'sur_place'],
            ['type_urgence' => 'medical',  'latitude' => 14.6800, 'longitude' => -17.4300, 'adresse' => 'Medina, Dakar',          'description' => 'Femme enceinte en detresse',     'statut' => 'en_attente'],
            ['type_urgence' => 'autre',    'latitude' => 14.7300, 'longitude' => -17.4600, 'adresse' => 'Guediawaye',             'description' => 'Inondation quartier residentiel','statut' => 'termine'],
        ];
        foreach ($incidents as $i) {
            Incident::create($i);
        }
    }
}
