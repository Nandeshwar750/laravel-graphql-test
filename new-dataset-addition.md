# Adding Categories to Posts in a Laravel GraphQL Project

This guide will walk you through the process of adding a **Category** model to your Laravel project, associating it with posts, and exposing category data through your GraphQL API. This is a common pattern for blogs, news sites, and content management systems.

---

## 1. Create the Category Model and Migration

Generate a new model and migration for categories:
```sh
php artisan make:model Category -m
```
This creates `app/Models/Category.php` and a migration file in `database/migrations/`.

Edit the migration to include fields like `name`, `slug`, `description`, and `is_active`:
```php
$table->string('name');
$table->string('slug')->unique();
$table->text('description')->nullable();
$table->boolean('is_active')->default(true);
```

---

## 2. Add a Category Reference to Posts

Create a migration to add a `category_id` foreign key to the `posts` table:
```sh
php artisan make:migration add_category_id_to_posts_table --table=posts
```
In the migration, add:
```php
$table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
```
This allows each post to optionally belong to a category.

---

## 3. Update the Post and Category Models

**In `app/Models/Post.php`:**
- Add `'category_id'` to the `$fillable` array.
- Add the relationship:
```php
public function category() {
    return $this->belongsTo(Category::class);
}
```

**In `app/Models/Category.php`:**
- Add the relationship:
```php
public function posts() {
    return $this->hasMany(Post::class);
}
```
- Add fillable fields and (optionally) a slug generator in the model's `boot` method.

---

## 4. Create a Category Factory and Seeder

Generate a factory:
```sh
php artisan make:factory CategoryFactory
```
Edit the factory to generate realistic fake data for categories.

Generate a seeder:
```sh
php artisan make:seeder CategorySeeder
```
In the seeder, create some predefined and random categories using the factory.

---

## 5. Update the DatabaseSeeder and PostSeeder

- In `DatabaseSeeder.php`, add `CategorySeeder` before `PostSeeder` so categories exist before posts are created.
- In `PostSeeder.php`, assign a random `category_id` to each post:
```php
'category_id' => $categories->random()->id,
```

---

## 6. Run Migrations and Seeders

To apply all changes and seed your database:
```sh
php artisan migrate:fresh --seed
```
Or, to run only the category and post seeders:
```sh
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=PostSeeder
```

---

## 7. Update the GraphQL Schema

Edit `graphql/schema.graphql` to:
- Add a `Category` type with fields and a `posts` relationship.
- Add a `category` and `categories` query to the `Query` type.
- Update the `Post` type to include the `category` field.
- Add mutations for creating, updating, and deleting categories if needed.

Example:
```graphql
type Category {
  id: ID!
  name: String!
  slug: String!
  description: String
  is_active: Boolean!
  posts: [Post!]! @hasMany
}

type Post {
  # ...
  category: Category @belongsTo
}

type Query {
  categories: [Category!]! @all
  category(id: ID, slug: String): Category @find
}
```

**Important:** After editing the schema, clear the Lighthouse cache:
```sh
php artisan lighthouse:clear-cache
```

---

## 8. Test Your GraphQL Endpoints

Open GraphiQL or your preferred GraphQL client and run queries like:
```graphql
query {
  categories {
    id
    name
    posts {
      id
      title
    }
  }
}
```

You should see categories and their related posts.

---

## 9. Tips for New Learners

- **Always clear the Lighthouse cache** after changing your schema.
- Use factories and seeders to quickly generate test data.
- Use the GraphiQL Docs panel to explore available queries and types.
- If you get empty results, check your database and seeder logic.
- Use Tinker (`php artisan tinker`) to inspect your models and data.
- Keep your migrations, models, and schema in sync for a smooth development experience.

---

By following these steps, you can successfully add categories to your posts and expose them via a GraphQL API in Laravel. This pattern can be extended to other relationships and entities as your application grows.