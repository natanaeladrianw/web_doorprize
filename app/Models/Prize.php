<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prize extends Model
{
    protected $fillable = [
        'form_id',
        'category_id',
        'name',
        'description',
        'quantity',
        'order',
        'is_active',
        'preset_submission_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Form
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Relasi ke PrizeCategory
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PrizeCategory::class, 'category_id');
    }

    /**
     * Relasi ke Winner (pemenang hadiah ini - sudah disimpan)
     */
    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }

    /**
     * Relasi ke preset submission (pemenang yang dipilih tapi belum disimpan)
     */
    public function presetSubmission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'preset_submission_id');
    }

    /**
     * Cek apakah hadiah sudah ada pemenangnya (tersimpan di winners)
     */
    public function hasWinner(): bool
    {
        return $this->winner()->exists();
    }

    /**
     * Cek apakah hadiah sudah ada preset winner
     */
    public function hasPresetWinner(): bool
    {
        return $this->preset_submission_id !== null;
    }

    /**
     * Scope untuk prize yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk prize yang belum ada pemenangnya
     */
    public function scopeAvailable($query)
    {
        return $query->whereDoesntHave('winner');
    }
}
