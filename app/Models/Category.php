<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class Category extends Model
{
    use SoftDeletes, AsSource, Filterable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * İzin verilen filtreler
     */
    protected $allowedFilters = [
        'name'        => Like::class,
        'slug'        => Like::class,
        'is_active'   => Where::class,
    ];

    /**
     * İzin verilen sıralama alanları
     */
    protected $allowedSorts = [
        'id',
        'name',
        'slug',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Kategori silindiğinde alt kayıtları varsayılan kategoriye taşı
        static::deleting(function ($category) {
            $defaultCategory = self::where('slug', 'default')->first();
            
            if ($defaultCategory && $category->id !== $defaultCategory->id) {
                TimeSlot::where('category_id', $category->id)
                    ->update(['category_id' => $defaultCategory->id]);
            }
        });

        // Kategori pasif yapıldığında alt slotları pasif yap
        static::updated(function ($category) {
            if (!$category->is_active) {
                $category->timeSlots()->update(['is_active' => false]);
            }
        });
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }
}
