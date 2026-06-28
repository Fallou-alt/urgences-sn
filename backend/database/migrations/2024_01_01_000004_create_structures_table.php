<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('sigle')->nullable();
            $table->enum('type', ['pompiers','samu','police','gendarmerie','marine','protection_civile','autre']);
            $table->string('region')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Ajouter structure_id dans agents
        Schema::table('agents', function (Blueprint $table) {
            $table->foreignId('structure_id')->nullable()->constrained('structures')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['structure_id']);
            $table->dropColumn('structure_id');
        });
        Schema::dropIfExists('structures');
    }
};
