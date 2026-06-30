<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->enum('type_urgence', ['incendie', 'accident', 'medical', 'autre']);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('adresse')->nullable();
            $table->text('description')->nullable();
            $table->string('citoyen_nom')->nullable();
            $table->string('citoyen_telephone')->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'AFFECTE', 'EN_ROUTE', 'SUR_PLACE', 'TERMINE', 'ANNULE'])->default('EN_ATTENTE');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_intervention')->nullable();
            $table->foreignId('structure_id')->nullable()->constrained('structures')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
