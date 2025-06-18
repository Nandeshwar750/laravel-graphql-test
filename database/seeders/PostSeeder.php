<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Post::create([
                'title' => "First Post by {$user->name}",
                'content' => 'This is the content of the first post.',
                'status' => 'PUBLISHED',
                'user_id' => $user->id,
            ]);

            Post::create([
                'title' => "Draft Post by {$user->name}",
                'content' => 'This is a draft post content.',
                'status' => 'DRAFT',
                'user_id' => $user->id,
            ]);
        }
    }
}
