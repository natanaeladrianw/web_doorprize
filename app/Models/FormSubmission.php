<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_id',
        'submission_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'submission_data' => 'array',
    ];

    /**
     * Relasi ke Form
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Relasi ke Winners (bisa memenangkan banyak hadiah)
     */
    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    /**
     * Cek apakah sudah pernah menang (untuk backward compatibility)
     */
    public function hasWon(): bool
    {
        return $this->winners()->exists();
    }

    /**
     * Jumlah hadiah yang dimenangkan
     */
    public function winCount(): int
    {
        return $this->winners()->count();
    }
}
