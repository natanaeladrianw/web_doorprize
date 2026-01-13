<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    protected $fillable = [
        'form_submission_id',
        'prize_id',
        'prize_name',
        'selection_method',
        'selected_by',
        'selected_at',
    ];

    protected $casts = [
        'selected_at' => 'datetime',
    ];

    /**
     * Relasi ke FormSubmission
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    /**
     * Relasi ke Prize
     */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * Relasi ke User (Admin yang memilih pemenang)
     */
    public function selector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }
}
