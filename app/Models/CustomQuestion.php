<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomQuestion extends Model
{
    use HasFactory;
    protected $table = 'custom_questions';
    protected $fillable = [
        'alphabet', 'question', 'answer', 'created_at', 'updated_at', 'room', 'title', 'is_public'
    ];
}
