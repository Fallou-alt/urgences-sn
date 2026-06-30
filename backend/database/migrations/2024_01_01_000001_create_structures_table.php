<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('sigle')->nullable();
            $table->enum('type', ['pompiers', 'samu', 'police', 'gendarmerie', 'marine', 'protection_civile', 'autre']);
            $table->string('region')->nullable();
            $table->string('departement')->nullable();
            $table->string('commune')->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Ajouter les clés étrangères croisées maintenant que les deux tables existent
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('structure_id')->references('id')->on('structures')->nullOnDelete();
        });

        Schema::table('structures', function (Blueprint $table) {
            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['structure_id']);
        });
        Schema::dropIfExists('structures');
    }
};
