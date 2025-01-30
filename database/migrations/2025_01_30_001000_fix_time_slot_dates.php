<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $slots = DB::table('time_slots')->get();

        foreach ($slots as $slot) {
            $date = $slot->date ? Carbon::parse($slot->date) : now();
            
            DB::table('time_slots')
                ->where('id', $slot->id)
                ->update([
                    'start_time' => $date->format('Y-m-d') . ' ' . Carbon::parse($slot->start_time)->format('H:i:s'),
                    'end_time' => $date->format('Y-m-d') . ' ' . Carbon::parse($slot->end_time)->format('H:i:s'),
                ]);
        }
    }

    public function down(): void
    {
        // Bu migration'ın geri alınması gerekmiyor
    }
}; 