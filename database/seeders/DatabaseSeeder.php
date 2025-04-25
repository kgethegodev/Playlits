<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        if(config('app.env') !== 'production') {
            User::factory()->create([
                'name' => 'Kgethego Masilo',
                'password' => Hash::make('password'),
                'email' => 'kgethi47@gmail.com',
            ]);
        }

        $tag_groups = [
            'mood' => ['Chill', 'Happy', 'Romantic', 'Aggressive', 'Hype', 'Dark', 'Uplifting', 'Moody', 'Mellow'],
            'genre' => ['Hip-Hop', 'R&B', 'Pop', 'Amapiano', 'Afrobeat', 'House', 'Rock', 'Indie', 'Jazz', 'Electronic'],
            'activity' => ['Gym', 'Study', 'Party', 'Driving', 'Cleaning', 'Coding', 'Sleep', 'Pre-drinks', 'Braai', 'Shower'],
        ];

        foreach ($tag_groups as $group => $tags) {
            foreach ($tags as $tag) {
                Tag::query()->UpdateOrCreate([
                    'name' => $tag,
                    'type' => $group,
                ]);
            }
        }
    }
}
