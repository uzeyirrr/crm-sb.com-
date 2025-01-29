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
        'time_slot_id',
        'team_id',
        'created_by',
        'date',
        'first_name',
        'last_name',
        'phone',
        'email',
        'contact_gender',
        'household_size',
        'latitude',
        'longitude',
        'street',
        'city',
        'customer_notes',
        'customer_age',
        'postal_code',
        'monthly_electricity_usage',
        'status',
        'roof_image'
    ];

    protected $casts = [
        'date' => 'date',
        'household_size' => 'integer',
        'customer_age' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'monthly_electricity_usage' => 'decimal:2'
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
        'team.name'       => Like::class,
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
        'team.name',
        'creator.name',
    ];

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
