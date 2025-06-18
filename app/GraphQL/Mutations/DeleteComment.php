<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use Illuminate\Support\Facades\Log;

final readonly class DeleteComment
{
    public function __invoke(null $_, array $args)
    {
        $comment = Comment::find($args['id']);
        if ($comment) {
            $comment->delete();
            Log::info('Comment deleted', ['id' => $args['id']]);
            return $comment;
        }
        Log::warning('Comment not found', ['id' => $args['id']]);
        return null;
    }
} 