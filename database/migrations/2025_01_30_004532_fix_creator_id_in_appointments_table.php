<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eğer creator_id sütunu yoksa ekle
        if (!Schema::hasColumn('appointments', 'creator_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eğer creator_id sütunu varsa kaldır
        if (Schema::hasColumn('appointments', 'creator_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropForeign(['creator_id']);
                $table->dropColumn('creator_id');
            });
        }
    }
};
