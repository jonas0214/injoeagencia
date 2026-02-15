<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['subtask_id', 'user_id', 'content', 'image_path'];

    // Relación con el usuario (si tienes modelo User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}