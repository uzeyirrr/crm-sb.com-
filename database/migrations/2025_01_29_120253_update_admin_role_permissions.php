<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Admin rolünü bul
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            // Mevcut izinleri al
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            // Takım izinlerini ekle
            $permissions = array_merge($permissions, [
                'platform.teams' => true,
                'platform.teams.create' => true,
                'platform.teams.edit' => true
            ]);

            // İzinleri güncelle
            DB::table('roles')
                ->where('slug', 'admin')
                ->update(['permissions' => json_encode($permissions)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Admin rolünü bul
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            // Mevcut izinleri al
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            // Takım izinlerini kaldır
            unset($permissions['platform.teams']);
            unset($permissions['platform.teams.create']);
            unset($permissions['platform.teams.edit']);

            // İzinleri güncelle
            DB::table('roles')
                ->where('slug', 'admin')
                ->update(['permissions' => json_encode($permissions)]);
        }
    }
};
