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
        'name', 'release_at', 'created_at', 'updated_at'
    ];
    protected $dates = [
        'release_at'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function releasesQuestions(): HasMany
    {
        return $this->hasMany(Question::class)->whereNull('release_at');
//        $date = now()->subDays(15)->toDateString();
//        return $this->hasMany(Question::class)->where('release_at', '<=', $date)->orWhereNull('release_at');
    }
}
