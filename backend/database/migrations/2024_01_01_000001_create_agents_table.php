<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant')->unique();
            $table->string('mot_de_passe');
            $table->string('nom');
            $table->string('prenom');
            $table->enum('role', ['admin', 'pompier', 'samu']);
            $table->boolean('actif')->default(true);
            $table->string('token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agents');
    }
};
