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

class Appointment extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'contact_gender',
        'household_size',
        'customer_age',
        'street',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'date',
        'time_slot_id',
        'creator_id',
        'monthly_electricity_usage',
        'status',
        'customer_notes',
        'roof_image'
    ];

    protected $casts = [
        'date' => 'date',
        'household_size' => 'integer',
        'customer_age' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'monthly_electricity_usage' => 'float'
    ];

    protected $allowedFilters = [
        'date'            => WhereDateStartEnd::class,
        'status'          => Where::class,
        'first_name'      => Like::class,
        'last_name'       => Like::class,
        'phone'           => Like::class,
        'email'           => Like::class,
        'city'           => Like::class,
        'timeSlot.name'   => Like::class,
        'creator.name'    => Like::class,
    ];

    protected $allowedSorts = [
        'date',
        'first_name',
        'last_name',
        'status',
        'created_at',
        'city',
        'timeSlot.name',
        'creator.name',
    ];

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function isEditable()
    {
        return $this->status !== 'completed' && $this->status !== 'cancelled';
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute()
    {
        return "{$this->street}, {$this->postal_code} {$this->city}";
    }
}
