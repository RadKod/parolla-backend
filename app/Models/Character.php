<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Sushi\Sushi;
use Transliterator;

class Character extends Model
{
    use Sushi;

    protected $keyType = 'string';

    public function getRows(): array
    {
        $alphabet = collect(['ç', 'ı', 'ö', 'ş', 'ü']);
        $exclude = ['x', 'q', 'w'];

        foreach (range('a', 'z') as $character)
            if (!in_array($character, $exclude))
                $alphabet->push($character);

        return $alphabet->sortBy(null, SORT_LOCALE_STRING)
            ->map(fn($char) => ['character' => Transliterator::create("tr-Upper")->transliterate($char)])
            ->toArray();
    }

    public function getQuestionCountAttribute(): int
    {
        return Question::query()->forCharacter($this->character)->count();
    }
}
