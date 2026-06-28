<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('structures', function (Blueprint $table) {
            $table->string('adresse')->nullable()->after('region');
            $table->string('responsable_nom')->nullable()->after('email');
            $table->string('responsable_titre')->nullable()->after('responsable_nom');
        });
    }

    public function down(): void {
        Schema::table('structures', function (Blueprint $table) {
            $table->dropColumn(['adresse', 'responsable_nom', 'responsable_titre']);
        });
    }
};
