# Laravel GraphQL API

A Laravel-based GraphQL API implementation that demonstrates a blog system with users, posts, and comments. This project showcases how to build a modern GraphQL API using Laravel and Lighthouse. It can be used as a starter kit for building GraphQL APIs with Laravel.

## Features

- **User Management**
  - Create users with roles (ADMIN, USER)
  - Fetch users with their posts
  - Input validation for user creation

- **Post Management**
  - Create, update, and fetch posts
  - Filter posts by status (DRAFT, PUBLISHED, ARCHIVED)
  - Associate posts with users

- **Comment System**
  - Create and delete comments
  - Associate comments with posts and users

- **GraphQL Features**
  - Type-safe schema definitions
  - Custom resolvers for complex queries
  - Input validation
  - Relationship handling
  - Enum types for status and roles

## Using as a Starter Kit

This project is designed to be used as a starter kit for building GraphQL APIs with Laravel. It provides:

- **Complete GraphQL Setup**: Pre-configured with Lighthouse, GraphiQL, and necessary middleware
- **Basic CRUD Operations**: Ready-to-use queries and mutations for common operations
- **Authentication Ready**: CSRF protection and session handling configured
- **Type System**: Example of using enums, relationships, and custom types
- **Testing Setup**: Comprehensive test suite demonstrating GraphQL testing
- **Documentation**: Well-documented schema and example queries

To use as a starter kit:

1. Clone this repository
2. Follow the installation steps below
3. Customize the schema in `graphql/schema.graphql`
4. Add your own resolvers in `app/GraphQL/`
5. Modify the models and migrations as needed

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or PostgreSQL
- Laravel 10.x

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd laravel-graphql-test
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_graphql_test
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

## GraphQL Playground

GraphQL Playground is an interactive, in-browser GraphQL IDE that helps you explore and test your GraphQL API. It provides features like:
- Interactive documentation
- Query autocompletion
- Real-time error reporting
- Query history
- Multiple tabs for different queries

### Setting up GraphQL Playground

1. Start your Laravel development server:
```bash
php artisan serve
```

2. Access GraphQL Playground:
   - Open your browser and navigate to `http://localhost:8000/graphiql`
   - This is the interactive GraphQL IDE where you can write and test queries
   - The GraphQL endpoint (`http://localhost:8000/graphql`) only accepts POST requests and should not be accessed directly in the browser

3. Important Notes:
   - The GraphQL endpoint (`/graphql`) only accepts POST requests
   - Always use the GraphiQL interface (`/graphiql`) for testing and development
   - Direct browser access to `/graphql` will not work as it requires POST requests
   - GraphiQL automatically handles the correct HTTP methods and headers

4. Example Playground Usage:
   - The left panel is where you write your queries
   - The right panel shows the results
   - The "DOCS" tab on the right shows your API documentation
   - The "SCHEMA" tab shows your complete GraphQL schema

5. Try this example query in the Playground:
```graphql
{
  users {
    id
    name
    email
    role
    posts {
      id
      title
      status
    }
  }
}
```

6. For mutations, use this format:
```graphql
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
```

### Playground Features

- **Query Variables**: Define variables for your queries
- **Headers**: Add custom headers (e.g., for authentication)
- **Tabs**: Work on multiple queries simultaneously
- **History**: Access your previous queries
- **Schema Explorer**: Browse available types and fields
- **Query Validation**: Real-time validation of your queries

## GraphQL Schema

The project uses a strongly-typed GraphQL schema defined in `graphql/schema.graphql`. Here are some example queries and mutations:

### Queries

1. Fetch all users:
```graphql
{
  users {
    id
    name
    email
    role
    posts {
      id
      title
      status
    }
  }
}
```

2. Filter posts by status:
```graphql
{
  postsByStatus(status: PUBLISHED) {
    id
    title
    status
    user {
      name
    }
  }
}
```

### Mutations

1. Create a new user:
```graphql
mutation {
  createUser(input: {
    name: "John Doe"
    email: "john@example.com"
    password: "password123"
    role: USER
  }) {
    id
    name
    email
    role
  }
}
```

2. Create a new post:
```graphql
mutation {
  createPost(input: {
    title: "My First Post"
    content: "This is the content of my first post"
    status: PUBLISHED
    user_id: 1
  }) {
    id
    title
    status
    user {
      name
    }
  }
}
```

## Testing

The project includes comprehensive test coverage for all GraphQL operations. Run the tests using:

```bash
php artisan test
```

## Project Structure

- `app/GraphQL/` - Contains GraphQL resolvers and mutations
- `graphql/schema.graphql` - The GraphQL schema definition
- `tests/Feature/GraphQLTest.php` - Test cases for GraphQL operations
- `app/Models/` - Eloquent models with relationships
- `database/migrations/` - Database migrations
- `database/seeders/` - Database seeders for testing

## Development Workflow

1. **Schema First**: Define your types and operations in `graphql/schema.graphql`
2. **Create Resolvers**: Implement the logic in `app/GraphQL/`
3. **Test**: Write tests in `tests/Feature/GraphQLTest.php`
4. **Document**: Update the schema with proper descriptions
5. **Iterate**: Use GraphiQL to test and refine your API

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
