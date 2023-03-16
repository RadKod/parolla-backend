<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Alphabet;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = [
        'alphabet_id', 'question', 'answer', 'created_at', 'updated_at', 'release_at'
    ];

    protected $dates = [
        'release_at'
    ];

    public function alphabet(): BelongsTo
    {
        return $this->belongsTo(Alphabet::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'id', 'id');
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

    /**
     * @param $query
     * @param $release_at
     * @return mixed
     */
    public function scopeRelease($query, $release_at)
    {
        // if than 15 day bigger than now
        if ($release_at) {
            $date = now()->subDays(15)->toDateString();
            return $query->where('release_at', '<=', $date)->orWhereNull('release_at');
        }
    }
}
