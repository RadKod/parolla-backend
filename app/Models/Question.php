<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = [
        'character', 'question', 'answer', 'created_at', 'updated_at'
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character', 'character');
    }

    /**
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('question', 'like', '%' . $search . '%')->orWhere('answer', 'like', '%' . $search . '%');
    }

    /**
     * @param $query
     * @param $character
     * @return mixed
     */
    public function scopeForCharacter($query, $character)
    {
        return $query->where('character', $character);
    }
}
