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

        Question::query()->create([
            'character' => 'A',
            'question' => 'Türkiye nin başkenti?',
            'answer' => 'Ankara'
        ]);
    }
}
