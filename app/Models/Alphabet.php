<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alphabet extends Model
{
    use HasFactory;
    protected $table = 'alphabet';
    protected $fillable = [
        'name', 'created_at', 'updated_at'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
