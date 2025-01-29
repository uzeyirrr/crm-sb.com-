<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id')->constrained()->onDelete('cascade');
        });

        // Mevcut time_slots kayıtlarını varsayılan kategoriye ata
        $defaultCategory = DB::table('categories')->where('slug', 'default')->first();
        if ($defaultCategory) {
            DB::table('time_slots')->update(['category_id' => $defaultCategory->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
