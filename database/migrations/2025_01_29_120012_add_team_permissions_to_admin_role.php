<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchid\Platform\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            $permissions = $adminRole->permissions;
            $permissions['platform.teams'] = 1;
            $permissions['platform.teams.create'] = 1;
            $permissions['platform.teams.edit'] = 1;
            
            $adminRole->permissions = $permissions;
            $adminRole->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            $permissions = $adminRole->permissions;
            unset($permissions['platform.teams']);
            unset($permissions['platform.teams.create']);
            unset($permissions['platform.teams.edit']);
            
            $adminRole->permissions = $permissions;
            $adminRole->save();
        }
    }
};
