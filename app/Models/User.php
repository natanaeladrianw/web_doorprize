<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Permission;
use App\Models\Form;
use App\Models\Winner;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah input hadiah (admin terbatas)
     */
    public function isInputHadiah(): bool
    {
        return $this->role === 'input_hadiah';
    }

    /**
     * Cek apakah user bisa mengakses admin panel
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin() || $this->isInputHadiah();
    }

    /**
     * Cek apakah user bisa mengelola pemenang (undian)
     */
    public function canManageWinners(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Relasi ke Forms yang dibuat
     */
    public function createdForms()
    {
        return $this->hasMany(Form::class, 'created_by');
    }

    /**
     * Relasi ke Winners yang dipilih
     */
    public function selectedWinners()
    {
        return $this->hasMany(Winner::class, 'selected_by');
    }

    // Permission relationship and checks
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->relationLoaded('permissions')) {
            $this->load('permissions');
        }

        return $this->permissions->contains('name', $permission);
    }
}
