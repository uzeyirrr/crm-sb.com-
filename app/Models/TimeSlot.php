<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class TimeSlot extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'interval',
        'max_appointments',
        'is_active',
        'description',
        'category_id'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:00',
        'end_time' => 'datetime:H:00',
        'is_active' => 'boolean',
        'interval_hours' => 'integer'
    ];

    protected $allowedFilters = [
        'name'             => Like::class,
        'is_active'        => Where::class,
        'max_appointments' => Where::class,
    ];

    protected $allowedSorts = [
        'name',
        'start_time',
        'end_time',
        'is_active',
        'max_appointments',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Kategori pasif olduğunda slotları pasif yap
        static::updating(function ($timeSlot) {
            if ($timeSlot->category && !$timeSlot->category->is_active) {
                $timeSlot->is_active = false;
            }
        });
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isAvailableForDate($date)
    {
        $appointmentsCount = $this->appointments()
            ->whereDate('date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $this->is_active && $appointmentsCount < $this->max_appointments;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
