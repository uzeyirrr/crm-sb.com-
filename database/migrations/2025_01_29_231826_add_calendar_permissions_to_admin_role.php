<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Admin rolünü bul
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            // Mevcut izinleri al
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            // Takvim izinlerini ekle
            $permissions['platform.calendar'] = 1;

            // İzinleri güncelle
            DB::table('roles')
                ->where('slug', 'admin')
                ->update(['permissions' => json_encode($permissions)]);
        }
    }

    public function down(): void
    {
        // Admin rolünü bul
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            // Mevcut izinleri al
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            // Takvim izinlerini kaldır
            unset($permissions['platform.calendar']);

            // İzinleri güncelle
            DB::table('roles')
                ->where('slug', 'admin')
                ->update(['permissions' => json_encode($permissions)]);
        }
    }
}; 