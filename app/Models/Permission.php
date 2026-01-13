<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Permission extends Model
{
    protected $fillable = ['name','description'];

    public function users()
    {
        return $this->belongsToMany(User::class); // relasi many-to-many
    }
}
