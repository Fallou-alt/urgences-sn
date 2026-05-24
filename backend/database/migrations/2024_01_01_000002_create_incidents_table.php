<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->enum('type_urgence', ['incendie', 'accident', 'medical', 'autre']);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('adresse')->nullable();
            $table->text('description')->nullable();
            $table->string('nom_citoyen')->nullable();
            $table->string('telephone_citoyen')->nullable();
            $table->enum('statut', ['en_attente', 'pris_en_charge', 'en_route', 'sur_place', 'termine'])->default('en_attente');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('incidents');
    }
};
