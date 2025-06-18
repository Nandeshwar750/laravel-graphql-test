<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::with('user')->get(); // Load user relationship
        $users = User::all();

        foreach ($posts as $post) {
            foreach ($users as $user) {
                if ($user->id !== $post->user_id) {
                    Comment::create([
                        'content' => "Great post by {$post->user->name}! - from {$user->name}",
                        'post_id' => $post->id,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
