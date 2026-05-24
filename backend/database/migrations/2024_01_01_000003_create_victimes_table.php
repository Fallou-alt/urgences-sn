<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('victimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->integer('age')->nullable();
            $table->enum('sexe', ['homme', 'femme', 'inconnu'])->default('inconnu');
            $table->string('telephone')->nullable();
            $table->enum('groupe_sanguin', ['A+','A-','B+','B-','AB+','AB-','O+','O-','inconnu'])->default('inconnu');
            $table->enum('etat', ['leger', 'grave', 'critique', 'decede', 'inconnu'])->default('inconnu');
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('victimes');
    }
};
