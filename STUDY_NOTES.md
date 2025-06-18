# Laravel GraphQL Study Guide

## Table of Contents
1. [Introduction to GraphQL](#introduction-to-graphql)
2. [Project Setup](#project-setup)
3. [GraphQL Schema Design](#graphql-schema-design)
4. [Resolvers and Mutations](#resolvers-and-mutations)
5. [Testing GraphQL APIs](#testing-graphql-apis)
6. [GraphQL Playground](#graphql-playground)
7. [Best Practices](#best-practices)

## Introduction to GraphQL

### What is GraphQL?
- GraphQL is a query language for APIs and a runtime for executing those queries
- Unlike REST, GraphQL allows clients to request exactly the data they need
- Single endpoint for all operations (queries and mutations)

### Key Concepts
- **Schema**: Defines the types and operations available in the API
- **Types**: Define the shape of data (e.g., User, Post)
- **Queries**: Operations to fetch data
- **Mutations**: Operations to modify data
- **Resolvers**: Functions that handle the logic for queries and mutations

## Project Setup

### Required Packages
```bash
composer require nuwave/lighthouse
```

### Configuration
1. Publish the configuration:
```bash
php artisan vendor:publish --tag=lighthouse-schema
```

2. Key configuration files:
- `config/lighthouse.php`: Main configuration
- `graphql/schema.graphql`: Schema definition
- `graphql/directives.graphql`: Custom directives

## GraphQL Schema Design

### Basic Type Definition
```graphql
type User {
    id: ID!
    name: String!
    email: String!
    posts: [Post!]! @hasMany
}

type Post {
    id: ID!
    title: String!
    content: String!
    user: User! @belongsTo
}
```

### Query Definition
```graphql
type Query {
    users: [User!]! @all
    user(id: ID! @eq): User @find
    posts: [Post!]! @all
    post(id: ID! @eq): Post @find
}
```

### Mutation Definition
```graphql
type Mutation {
    createUser(input: CreateUserInput! @spread): User! @create
    updateUser(id: ID!, input: UpdateUserInput! @spread): User! @update
    deleteUser(id: ID!): User! @delete
}
```

## Resolvers and Mutations

### Creating Resolvers
1. Create resolver class:
```php
namespace App\GraphQL\Queries;

class UsersResolver
{
    public function __invoke($_, array $args)
    {
        return User::all();
    }
}
```

2. Register in schema:
```graphql
type Query {
    users: [User!]! @field(resolver: "App\\GraphQL\\Queries\\UsersResolver")
}
```

### Creating Mutations
1. Create mutation class:
```php
namespace App\GraphQL\Mutations;

class CreateUser
{
    public function __invoke($_, array $args)
    {
        return User::create($args['input']);
    }
}
```

2. Register in schema:
```graphql
type Mutation {
    createUser(input: CreateUserInput! @spread): User! @field(resolver: "App\\GraphQL\\Mutations\\CreateUser")
}
```

## Testing GraphQL APIs

### Test Structure
```php
namespace Tests\Feature;

class GraphQLTest extends TestCase
{
    public function test_can_query_users()
    {
        $response = $this->graphQL('
            query {
                users {
                    id
                    name
                    email
                }
            }
        ');

        $response->assertJson([
            'data' => [
                'users' => []
            ]
        ]);
    }
}
```

### Testing Mutations
```php
public function test_can_create_user()
{
    $response = $this->graphQL('
        mutation {
            createUser(input: {
                name: "John Doe"
                email: "john@example.com"
            }) {
                id
                name
                email
            }
        }
    ');

    $response->assertJson([
        'data' => [
            'createUser' => [
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ]
        ]
    ]);
}
```

## GraphQL Playground

### Setup
1. Install GraphiQL:
```bash
composer require mll-lab/laravel-graphql-playground
```

2. Access at: `http://your-app/graphql-playground`

### Features
- Interactive API documentation
- Query builder
- Schema explorer
- Request/response viewer

## Best Practices

### Schema Design
1. Use meaningful type names
2. Make fields nullable when appropriate
3. Use input types for mutations
4. Implement proper validation

### Performance
1. Use pagination for large datasets
2. Implement caching where appropriate
3. Use field selection to limit data

### Security
1. Implement proper authentication
2. Use middleware for authorization
3. Validate input data
4. Rate limiting

### Code Organization
1. Keep resolvers in separate files
2. Use type definitions for complex objects
3. Implement proper error handling
4. Follow Laravel naming conventions

## Common Issues and Solutions

### 1. Schema Loading Issues
- Check file permissions
- Verify schema file location
- Clear configuration cache

### 2. Resolver Not Found
- Check namespace
- Verify class name
- Clear route cache

### 3. Type Definition Errors
- Check for circular dependencies
- Verify field types
- Ensure all required fields are defined

## Additional Resources

### Documentation
- [Lighthouse Documentation](https://lighthouse-php.com/)
- [GraphQL Official Documentation](https://graphql.org/)
- [Laravel Documentation](https://laravel.com/docs)

### Tools
- GraphQL Playground
- Postman
- Insomnia

### Learning Resources
- GraphQL Fundamentals
- Laravel Best Practices
- API Design Patterns

## Conclusion
This study guide covers the essential aspects of implementing GraphQL in a Laravel application. Remember to:
1. Start with a well-designed schema
2. Implement proper testing
3. Follow security best practices
4. Use appropriate tools for development
5. Keep code organized and maintainable 