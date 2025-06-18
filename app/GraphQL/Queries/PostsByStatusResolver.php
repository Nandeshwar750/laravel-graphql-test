<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

final readonly class PostsByStatusResolver
{
    /** @param  array{status: string}  $args */
    public function __invoke(null $_, array $args)
    {
        $posts = Post::where('status', $args['status'])->get()->all();
        Log::info('PostsByStatusResolver called', ['status' => $args['status'], 'count' => count($posts)]);
        
        if (empty($posts)) {
            throw new \Exception('No posts found with status: ' . $args['status']);
        }
        
        return $posts;
    }
}
