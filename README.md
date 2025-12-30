# Laravel Notion API Package

This package provides an elegant and comprehensive interface for interacting with the Notion API within Laravel applications. It allows you to query, create, update, and manage Notion pages, databases, blocks, comments, users, and more, with a fluent and intuitive API.

> **Note**: This package is a fork maintained for stability and bug fixes on-demand. Updates and improvements are applied as needed, and tests are currently not fully correct. For the latest features, consider the original package.

## Installation

The package is compatible with Laravel 8, 9, 10, and 11. The minimum PHP requirement is 8.0.

1. Install the package via Composer:

   ```bash
   composer require jensvandewiel/laravel-notion-api
   ```

2. Obtain your Notion API access token by following the instructions in the Notion developer documentation at https://developers.notion.com/reference/authentication.

3. Add the token to your application's .env file:

   ```bash
   NOTION_API_TOKEN=your_access_token_here
   ```

4. Publish the configuration file (optional, for customization):

   ```bash
   php artisan vendor:publish --provider="Jensvandewiel\LaravelNotionApi\LaravelNotionApiServiceProvider"
   ```

## Configuration

The package uses the NOTION_API_TOKEN environment variable for authentication. You can customize the API version and other settings in the published config file.

## Basic Usage

Access Notion endpoints through the Notion facade:

```php
use Jensvandewiel\LaravelNotionApi\Notion;

$notion = new Notion(env('NOTION_API_TOKEN'));
```

Or use the facade:

```php
use Notion;

Notion::pages()->find('page_id');
```

## Entities

The package provides several entity classes that represent Notion objects:

- Page: Represents a Notion page
- Database: Represents a Notion database
- DataSource: Represents a Notion data source
- Block: Represents a Notion block
- User: Represents a Notion user
- Comment: Represents a Notion comment
- FileUpload: Represents a file upload
- Token: Represents an OAuth token
- NotionParent: Represents parent relationships

Each entity provides methods to access and manipulate its properties.

## Pages

### Retrieving a Page

To retrieve a page by its ID:

```php
$page = Notion::pages()->find('page_id');
```

This returns a Page entity with all its properties and content.

### Creating a Page in a Database

To create a new page in a database:

```php
$page = new Page();
$page->setTitle('Name', 'My New Page');
$page->setText('Description', 'This is a description');

$createdPage = Notion::pages()->createInDatabase('database_id', $page);
```

### Creating a Page as a Child of Another Page

```php
$page = new Page();
$page->setTitle('Name', 'My New Page');

$createdPage = Notion::pages()->createInPage('parent_page_id', $page);
```

### Updating a Page

To update an existing page:

```php
$page = Notion::pages()->find('page_id');
$page->setTitle('Name', 'Updated Title');
$page->setText('Description', 'Updated description');

$updatedPage = Notion::pages()->update($page);
```

### Archiving a Page

```php
Notion::pages()->archive('page_id');
```

### Moving a Page

```php
Notion::pages()->move('page_id', ['parent' => ['page_id' => 'new_parent_id']]);
```

## Databases

### Retrieving a Database

```php
$database = Notion::databases()->find('database_id');
```

### Creating a Database

```php
$database = new Database();
// Set properties...

$createdDatabase = Notion::databases()->create($database);
```

### Updating a Database

```php
$database = Notion::databases()->find('database_id');
// Modify properties...

$updatedDatabase = Notion::databases()->update($database);
```

### Querying a Database

```php
$query = Notion::databases()->query('database_id');
$pages = $query->filter(['property' => 'Name', 'text' => ['contains' => 'search term']])->get();
```

## Blocks

### Retrieving Block Children

```php
$blocks = Notion::blocks()->getChildren('block_id');
```

### Appending Block Children

```php
$blocks = [
    ['type' => 'paragraph', 'paragraph' => ['rich_text' => [['type' => 'text', 'text' => ['content' => 'New paragraph']]]]]
];

Notion::blocks()->appendChildren('block_id', $blocks);
```

### Updating a Block

```php
Notion::blocks()->update('block_id', ['type' => 'paragraph', 'paragraph' => ['rich_text' => [['type' => 'text', 'text' => ['content' => 'Updated content']]]]);
```

### Deleting a Block

```php
Notion::blocks()->delete('block_id');
```

## Comments

### Creating a Comment

```php
Notion::comments()->create('page_id', 'Comment text');
```

### Retrieving Comments

```php
$comments = Notion::comments()->list('page_id');
```

## Users

### Retrieving Users

```php
$users = Notion::users()->list();
```

### Retrieving a Specific User

```php
$user = Notion::users()->find('user_id');
```

## Search

### Searching

```php
$results = Notion::search('query')->query()->asCollection();
```

## File Uploads

### Creating a File Upload

```php
$upload = Notion::fileUploads()->create('file_name', 'file_type');
```

### Sending a File Upload

```php
Notion::fileUploads()->send($upload->getId(), $fileContent);
```

### Completing a File Upload

```php
Notion::fileUploads()->complete($upload->getId());
```

## Property Types

The package supports all Notion property types:

- Title: setTitle('property_name', 'text')
- Rich Text: setText('property_name', 'text')
- Number: setNumber('property_name', 123.45)
- Select: setSelect('property_name', 'option_name')
- Multi-Select: setMultiSelect('property_name', ['option1', 'option2'])
- Date: setDate('property_name', $startDate, $endDate)
- People: setPeople('property_name', ['user_id1', 'user_id2'])
- Files: (handled via file uploads)
- Checkbox: setCheckbox('property_name', true)
- URL: setUrl('property_name', 'https://example.com')
- Email: setEmail('property_name', 'email@example.com')
- Phone Number: setPhoneNumber('property_name', '123-456-7890')
- Relation: setRelation('property_name', ['page_id1', 'page_id2'])
- Place: setPlace('property_name', 'Location Name', 12.34, 56.78, 'Address')

## Filtering and Sorting

When querying databases, you can apply filters and sorting:

```php
$query = Notion::databases()->query('database_id');
$query->filter([
    'property' => 'Name',
    'text' => ['contains' => 'search']
]);
$query->sort([
    'property' => 'Created',
    'direction' => 'descending'
]);
$results = $query->get();
```

## Error Handling

The package throws NotionException for API errors. Always wrap your calls in try-catch blocks:

```php
try {
    $page = Notion::pages()->find('page_id');
} catch (NotionException $e) {
    // Handle error
}
```

## Advanced Usage

### Working with Rich Text

Rich text is handled automatically for title and text properties. For more complex rich text manipulation, use the RichText entity.

### Custom Property Types

For unsupported property types, you can extend the Property class and implement the Modifiable interface.

### Batch Operations

For bulk operations, collect multiple requests and execute them in sequence.

## Contributing

Contributions are welcome. Please ensure all tests pass and follow the existing code style.

## License

This package is open-sourced software licensed under the MIT license.
