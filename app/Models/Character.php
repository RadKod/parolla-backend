<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use Transliterator;

/**
 * Character model which use `array` driver.
 *
 * TODO: Remove this model and use some data instance or helper for this.
 * @property mixed $character
 */
class Character extends Model
{
    use Sushi;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'character';

    /**
     * All characters in the alphabet.
     *
     * @return array data
     */
    public function getRows(): array
    {
        // TODO: Use better way for retrieve alphabet. It's just silly...
        $alphabet = collect(['ç', 'ı', 'ö', 'ş', 'ü']);
        $exclude = ['x', 'q', 'w'];

        foreach (range('a', 'z') as $character)
            if (!in_array($character, $exclude))
                $alphabet->push($character);

        return $alphabet->sortBy(null, SORT_LOCALE_STRING)
            ->map(fn($char) => ['character' => Transliterator::create("tr-Upper")->transliterate($char)])
            ->toArray();
    }

    /**
     * Retrieve questions count of this character.
     *
     * @return int total number of questions
     * @noinspection PhpUnused
     */
    public function getQuestionCountAttribute(): int
    {
        return Question::query()->forCharacter($this->character)->count();
    }

    /**
     * Retrieve releases count of this character.
     *
     * @return int total number of releases
     * @noinspection PhpUnused
     */
    public function getReleaseCountAttribute(): int
    {
        $date = now()->subDays(15)->toDateString();
        return Question::query()->forCharacter($this->character)
            ->where('release_at', '<=', $date
            )->orWhereNull('release_at'
            )->count();
    }
}
