<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Alphabet;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = [
        'alphabet_id', 'question', 'answer', 'created_at', 'updated_at'
    ];

    public function alphabet(): BelongsTo
    {
        return $this->belongsTo(Alphabet::class);
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
     * @param $alphabet_id
     * @return mixed
     */
    public function scopeAlphabet($query, $alphabet_id)
    {
        if ($alphabet_id) {
            return $query->where('alphabet_id', $alphabet_id);
        }
    }
}
