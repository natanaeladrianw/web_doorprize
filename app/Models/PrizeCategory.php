<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrizeCategory extends Model
{
    protected $fillable = [
        'form_id',
        'name',
        'order',
    ];

    /**
     * Relasi ke Form
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Relasi ke Prizes
     */
    public function prizes(): HasMany
    {
        return $this->hasMany(Prize::class, 'category_id');
    }
}
