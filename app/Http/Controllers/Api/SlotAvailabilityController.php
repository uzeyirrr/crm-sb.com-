<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SlotAvailabilityController extends Controller
{
    public function check(TimeSlot $timeSlot): JsonResponse
    {
        // Cache'den kontrol et
        $cacheKey = "slot_lock_{$timeSlot->id}";
        
        if (Cache::has($cacheKey)) {
            return response()->json(['available' => false]);
        }

        // Slot'un müsaitlik durumunu kontrol et
        $appointmentsCount = $timeSlot->appointments()
            ->where('status', '!=', 'cancelled')
            ->count();

        $isAvailable = $appointmentsCount < $timeSlot->max_appointments;

        if ($isAvailable) {
            // 5 dakikalık bir kilit ekle
            Cache::put($cacheKey, true, now()->addMinutes(5));
        }

        return response()->json(['available' => $isAvailable]);
    }
} 