<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class GraphQLTest extends TestCase
{
    use RefreshDatabase, MakesGraphQLRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_fetch_users()
    {
        $response = $this->graphQL('
            {
                users {
                    id
                    name
                    email
                    role
                }
            }
        ');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'users' => [
                        '*' => ['id', 'name', 'email', 'role']
                    ]
                ]
            ]);
    }

    public function test_can_fetch_single_user_with_posts()
    {
        $user = User::first();

        $response = $this->graphQL("
            {
                user(id: {$user->id}) {
                    id
                    name
                    email
                    posts {
                        id
                        title
                        status
                    }
                }
            }
        ");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user' => [
                        'id' => (string) $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ]);
    }

    public function test_can_create_user()
    {
        $response = $this->graphQL('
            mutation {
                createUser(input: {
                    name: "Test User"
                    email: "test@example.com"
                    password: "password123"
                    role: USER
                }) {
                    id
                    name
                    email
                    role
                }
            }
        ');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'createUser' => [
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                        'role' => 'USER',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_can_create_post()
    {
        $user = User::first();

        $response = $this->graphQL("
            mutation {
                createPost(input: {
                    title: \"Test Post\"
                    content: \"This is test content\"
                    status: PUBLISHED
                    user_id: {$user->id}
                }) {
                    id
                    title
                    content
                    status
                    user {
                        name
                    }
                }
            }
        ");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'createPost' => [
                        'title' => 'Test Post',
                        'content' => 'This is test content',
                        'status' => 'PUBLISHED',
                        'user' => [
                            'name' => $user->name
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_fetch_posts_with_comments()
    {
        $response = $this->graphQL('
            {
                posts {
                    id
                    title
                    status
                    user {
                        name
                    }
                    comments {
                        id
                        content
                        user {
                            name
                        }
                    }
                }
            }
        ');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'posts' => [
                        '*' => [
                            'id',
                            'title',
                            'status',
                            'user' => ['name'],
                            'comments' => [
                                '*' => [
                                    'id',
                                    'content',
                                    'user' => ['name']
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_filter_posts_by_status()
    {
        // Dump all posts to check database state
        $allPosts = Post::all();
        dump('All posts:', $allPosts->toArray());

        $response = $this->graphQL('
            {
                postsByStatus(status: PUBLISHED) {
                    id
                    title
                    status
                }
            }
        ');

        $response->assertStatus(200);
        dump('GraphQL Response:', $response->json());

        $posts = $response->json('data.postsByStatus');
        foreach ($posts as $post) {
            $this->assertEquals('PUBLISHED', $post['status']);
        }
    }

    public function test_validation_errors_on_invalid_input()
    {
        $response = $this->graphQL('
            mutation {
                createUser(input: {
                    name: ""
                    email: "invalid-email"
                    password: "123"
                    role: USER
                }) {
                    id
                    name
                    email
                }
            }
        ');

        $response->assertStatus(200)
            ->assertGraphQLErrorMessage('Validation failed for the field [createUser].');
    }

    public function test_can_update_post()
    {
        $post = Post::first();

        $response = $this->graphQL("
            mutation {
                updatePost(id: {$post->id}, input: {
                    title: \"Updated Title\"
                    status: ARCHIVED
                }) {
                    id
                    title
                    status
                }
            }
        ");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'updatePost' => [
                        'id' => (string) $post->id,
                        'title' => 'Updated Title',
                        'status' => 'ARCHIVED',
                    ]
                ]
            ]);
    }

    public function test_can_delete_comment()
    {
        $comment = Comment::first();

        $response = $this->graphQL("
            mutation {
                deleteComment(id: {$comment->id}) {
                    id
                }
            }
        ");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
