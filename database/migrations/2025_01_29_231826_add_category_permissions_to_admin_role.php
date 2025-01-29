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
        // Admin rolünü bul
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            // Mevcut izinleri al
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            // Kategori izinlerini ekle
            $newPermissions = [
                'platform.categories' => true,
                'platform.categories.create' => true,
                'platform.categories.edit' => true,
                'platform.categories.delete' => true
            ];

            $permissions = array_merge($permissions, $newPermissions);

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

            // Kategori izinlerini kaldır
            unset($permissions['platform.categories']);
            unset($permissions['platform.categories.create']);
            unset($permissions['platform.categories.edit']);
            unset($permissions['platform.categories.delete']);

            // İzinleri güncelle
            DB::table('roles')
                ->where('slug', 'admin')
                ->update(['permissions' => json_encode($permissions)]);
        }
    }
};
