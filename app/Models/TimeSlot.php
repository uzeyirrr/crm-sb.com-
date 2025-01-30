<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Carbon\Carbon;

class TimeSlot extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'interval_hours',
        'max_appointments',
        'is_active',
        'description',
        'category_id',
        'date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'interval_hours' => 'integer',
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    protected $dates = [
        'date',
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $allowedFilters = [
        'name'             => Like::class,
        'is_active'        => Where::class,
        'max_appointments' => Where::class,
        'date'            => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'name',
        'start_time',
        'end_time',
        'is_active',
        'max_appointments',
        'date',
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

    public function setStartTimeAttribute($value)
    {
        if ($this->date && is_string($value)) {
            $dateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $value);
            $this->attributes['start_time'] = $dateTime->format('Y-m-d H:i:s');
        }
    }

    public function setEndTimeAttribute($value)
    {
        if ($this->date && is_string($value)) {
            $dateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $value);
            $this->attributes['end_time'] = $dateTime->format('Y-m-d H:i:s');
        }
    }

    public function getStartTimeFormatted()
    {
        return $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : '-';
    }

    public function getEndTimeFormatted()
    {
        return $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : '-';
    }
}
