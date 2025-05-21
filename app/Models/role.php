<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class role extends Model
{
    protected $fillable = ['name'];

    // Связь с пользователями
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
    use HasFactory;

}

