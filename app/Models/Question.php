<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'character', 'question', 'answer', 'release_at', 'created_at', 'updated_at'
    ];

    protected $dates = [
        'release_at'
    ];

    /**
     * Retrieve character of this question.
     *
     * @return BelongsTo character model
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character', 'character');
    }

    /**
     * Scope query to specific search keyword.
     *
     * TODO: We don't need to use this simple scope.
     *
     * @param Builder $query
     * @param string $search
     * @return mixed
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('question', 'like', '%' . $search . '%')
            ->orWhere('answer', 'like', '%' . $search . '%');
    }

    /**
     * Scope query to specific character.
     *
     * TODO: We don't need to use this simple scope.
     *
     * @param Builder $query
     * @param string $character
     * @return mixed
     */
    public function scopeForCharacter(Builder $query, string $character): Builder
    {
        return $query->where('character', $character);
    }
}
