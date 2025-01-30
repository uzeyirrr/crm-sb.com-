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

            // Yeni izinleri ekle
            $newPermissions = [
                'platform.calendar' => true,
                'platform.appointments' => true,
                'platform.appointments.create' => true,
                'platform.appointments.edit' => true,
                'platform.slots' => true,
                'platform.slots.create' => true,
                'platform.slots.edit' => true,
                'platform.categories' => true,
                'platform.categories.create' => true,
                'platform.categories.edit' => true,
                'platform.categories.delete' => true,
                'platform.teams' => true,
                'platform.teams.create' => true,
                'platform.teams.edit' => true,
            ];

            // İzinleri birleştir
            $permissions = array_merge($permissions, $newPermissions);

            // Veritabanını güncelle
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

            // İzinleri kaldır
            unset($permissions['platform.calendar']);
            unset($permissions['platform.appointments']);
            unset($permissions['platform.appointments.create']);
            unset($permissions['platform.appointments.edit']);
            unset($permissions['platform.slots']);
            unset($permissions['platform.slots.create']);
            unset($permissions['platform.slots.edit']);
            unset($permissions['platform.categories']);
            unset($permissions['platform.categories.create']);
            unset($permissions['platform.categories.edit']);
            unset($permissions['platform.categories.delete']);
            unset($permissions['platform.teams']);
            unset($permissions['platform.teams.create']);
            unset($permissions['platform.teams.edit']);

            // Veritabanını güncelle
            DB::table('roles')
                ->where('slug', 'admin')
                ->update(['permissions' => json_encode($permissions)]);
        }
    }
};
