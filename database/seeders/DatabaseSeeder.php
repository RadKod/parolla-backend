<?php

namespace Database\Seeders;

use App\Models\Alphabet;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::query()->create([
            'username' => 'admin',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin')
        ]);

        $alphabets = [
            ['name' => 'A'],
            ['name' => 'B'],
            ['name' => 'C'],
            ['name' => 'Ç'],
            ['name' => 'D'],
            ['name' => 'E'],
            ['name' => 'F'],
            ['name' => 'G'],
            ['name' => 'H'],
            ['name' => 'I'],
            ['name' => 'İ'],
            ['name' => 'J'],
            ['name' => 'K'],
            ['name' => 'L'],
            ['name' => 'M'],
            ['name' => 'N'],
            ['name' => 'O'],
            ['name' => 'Ö'],
            ['name' => 'P'],
            ['name' => 'R'],
            ['name' => 'S'],
            ['name' => 'Ş'],
            ['name' => 'T'],
            ['name' => 'U'],
            ['name' => 'Ü'],
            ['name' => 'V'],
            ['name' => 'Y'],
            ['name' => 'Z'],
        ];

        Alphabet::query()->upsert($alphabets, ['name']);

        Question::query()->create([
            'alphabet_id' => 1,
            'question' => 'Türkiye nin başkenti?',
            'answer' => 'Ankara'
        ]);
    }
}
