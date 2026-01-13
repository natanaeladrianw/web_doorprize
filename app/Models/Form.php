<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke User (Admin yang membuat form)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke FormField
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    /**
     * Relasi ke FormSubmission
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Relasi ke Prize (Hadiah)
     */
    public function prizes(): HasMany
    {
        return $this->hasMany(Prize::class)->orderBy('order');
    }

    /**
     * Relasi ke PrizeCategory (Kategori Hadiah)
     */
    public function prizeCategories(): HasMany
    {
        return $this->hasMany(PrizeCategory::class)->orderBy('order');
    }
}
