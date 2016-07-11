## TypeRocket 3
Coding advanced WordPress themes became a blast. Be good looking and do cool stuff with less code.

See [http://typerocket.com](http://typerocket.com) for version 2. Version 3.0 is waiting official release and is in BETA.

### Requirements

- WordPress 4.5+
- PHP 5.4+

### Language

- English only

### What can you do with TypeRocket?

- You can make make post types, taxonomies and meta boxes with one line of code.
- Add fields to post types, comments, meta boxes, option pages, user profiles and even on the front-end inside theme template files.
- You can access a number of pre-made TypeRocket plugins for features like SEO and Theme Options installed directly into your theme.

### 2.0 Documentation

[https://typerocket.com/docs/](https://typerocket.com/docs/)

### Book Post Type Example

Gracefully configure WordPress without needing hooks.

- Add a "Book" post type
- Add a custom menu icon to "Book" (200+ icons)
- Add an "Author" taxonomy to "Book" post type

```php
// In your themes functions.php
include( 'typerocket/init.php' );

// Add taxonomy with custom "name" gracefully
$bookAuthor = tr_taxonomy('Author')->setId('book_author');

// Add post type with icon and taxonomy gracefully
$book = tr_post_type('Book')->setIcon('book')->apply($bookAuthor);
```

- Add a MetaBox to "Book"
- Set custom Title placeholder text

```php
$bookDetails = tr_meta_box('Details');
$book->apply($bookDetails)->setTitlePlaceholder('Enter Book Title');
```

- Use debug mode to get meta box content provider function
- Add custom fields for ISBN and Book Cover

```php
function add_meta_content_details() {
    $form = tr_form();
    echo $form->text('ISBN Number');
    echo $form->image('Book Cover');
}
```

![GitHub Logo](http://typerocket.com/github/typerocket-book-example.png)

*Note: Flush the WordPress permalinks when you register a post type.*

### Fillable Fields Example

By default TypeRocket will save any and all fields. This is just fine in many cases and makes life easy. When you need to filter, validate or fill only specific fields we have you covered.

Set the fields for the "Book" post type the only fillable fields to make things a little more secure. You can do this by creating a book controller and model inside the app folder.

`BooksModel.php` is the main file we care about.

```php
<?php // /app/Models/BooksModel.php
namespace App\Models;

use \TypeRocket\Models\PostTypesModel;

class BooksModel extends PostTypesModel
{
    protected $fillable = [
        'book_cover',
        'isbn_number'
    ];

    protected $postType = 'book';
}
```

`BooksController.php` needs to be created to handle the actions. Blank works just fine.

```php
<?php // /app/Controllers/BooksController.php
namespace App\Controllers;

use \TypeRocket\Controllers\PostTypesController;

class BooksController extends PostTypesController
{
}
```

Now only "ISBN Number" and "Book Cover" will be saved. However, because the resource controller/model has been specified, "books", only an administrator can manage the new fields.

### XKernel

You will need to create a new Kernel class called XKernel to specify the middleware you want to use. Middleware can be used for all kinds of fun things. In this case we want "books" to authenticate like posts.

`XKernel.php` manages the middleware for the resource and request types.

```php
<?php // /app/Http/XKernel.php
namespace App\Http;

class XKernel extends \TypeRocket\Http\Kernel
{

    protected $middleware = [
        'hookGlobal' =>
            [ \TypeRocket\Http\Middleware\AuthRead::class ],
        'restGlobal' =>
            [
                \TypeRocket\Http\Middleware\AuthRead::class,
                \TypeRocket\Http\Middleware\ValidateCsrf::class
            ],
        'noResource' =>
            [ \TypeRocket\Http\Middleware\AuthAdmin::class ],
        'users' =>
            [ \TypeRocket\Http\Middleware\IsUserOrCanEditUsers::class ],
        'posts' =>
            [ \TypeRocket\Http\Middleware\OwnsPostOrCanEditPosts::class ],
        'pages' =>
            [ \TypeRocket\Http\Middleware\OwnsPostOrCanEditPosts::class ],
        'comments' =>
            [ \TypeRocket\Http\Middleware\OwnsCommentOrCanEditComments::class ],
        'options' =>
            [ \TypeRocket\Http\Middleware\CanManageOptions::class ],
        'categories' =>
            [ \TypeRocket\Http\Middleware\CanManageCategories::class ],
        'tags' =>
            [ \TypeRocket\Http\Middleware\CanManageCategories::class ]
    ];
}

```

### Format Fields Example

Back at our `BooksModel` we can also format the fields when they are being saved. This allows us to filter and sanitize
data before it is saved.

- Make sure the "ISBN Number" doesn't contain invalid characters
- Cast the "Book Cover" to an integer since we reference it by the attachment ID.

```php
<?php // /app/Models/BooksModel.php
namespace App\Models;

use \TypeRocket\Models\PostTypesModel;

class BooksModel extends PostTypesModel
{
    protected $fillable = [
        'book_cover',
        'isbn_number'
    ];

    protected $postType = 'book';

    protected $format = [
        'book_cover' => 'intval',
        'isbn_number' => 'format_isbn'
    ];
}
```

Then,

```php
<?php // In your themes functions.php
function format_isbn($isbn) {
    $isbn = strtoupper($isbn);
    $isbn = preg_replace('/((?![Xx0-9-]+).)*/i', '', $isbn);
    return $isbn;
}
```

#### Filter Fields Hook

Alternatively, you can filter the fields you when do not have access to the model. The model filter
`tr_model_secure_fields` lets you access our fields so you can sanitize the data being saved.

```php
add_filter('tr_model_secure_fields', function($fields, $model) {
    if($model instanceof \TypeRocket\Models\BooksModel) {

        if(isset($fields['isbn_number'])) {
            $isbn = strtoupper($fields['isbn_number']);
            $isbn = preg_replace('/((?![Xx0-9-]+).)*/i', '', $isbn);
            $fields['isbn_number'] = $isbn;
        }

        if(isset($fields['book_cover'])) {
            $fields['book_cover'] = (int) $fields['book_cover'];
        }

    }
    return $fields;
}, 10, 2);
```

#### Wild Cards

TypeRocket also supports wildcards using dot notation for deeply nested fields.

##### Example: Existing Model

On an existing model like `PagesModel`... here we might have a custom event component used by a builder field with a
field that requires a link.

```php
// In your themes functions.php
add_action('tr_model', function($model) {
    if( $model instanceof \TypeRocket\Models\PagesModel ) {
        $model->appendFormatField('builder.*.event.button_link_location', 'esc_url');
    }
});
```

##### Example: New Model

On an `EventsModel`... here we might have a custom event component used by a matrix field with a field that
requires an attachment ID.

```php
<?php // /app/Models/EventsModel.php
namespace App\Models;

use \TypeRocket\Models\PostTypesModel;

class EventsModel extends PostTypesModel
{
    protected $fillable = ['matrix'];

    protected $postType = 'event';

    protected $format = [
       'matrix.*.event.photo' => 'intval'
    ];
}
```

### Custom Taxonomy Fields

You can also use the same design and principles to work with taxonomies. You will want to create both a Controller and Model when adding a custom taxonomy.

```php
<?php
$publisher = tr_taxonomy('Publisher', 'Publishers');
$publisher->addPostType('post');
$publisher->setMainForm(function() {
	$form = tr_form();
	echo $form->text('Company');
});
```

### Designers

We made TypeRocket with design in mind first. Inside and out. Writing your code to replace plugins is super simple. We give you a development mode so building your custom feature is as simple as copy and paste. Don't let that fool you though. TypeRocket uses all the best available programing patterns that are compatible with WordPress.

### Developers

- We know SOLID and avoid globals.
- We believe in reducing dependencies and building a system that is flexible and secure.
- You can Build your own custom Fields.
- You can create custom Controllers to manage REST requests in TypeRocket.
- Filter and validate before saving data to the database with hooks.
- TypeRocket is fast! We use autoload (PSR-4) against the TypeRocket namespace so there are no conflicts.
- Rich commenting and doc blocks to enhance IDE and coding experience.

### Story

We want you to be good looking and do cool stuff with less code; Whether you're a designer, developer or somewhere in between. This is why TypeRocket exists.

We want to enable everyone so they can build themes that are beautiful, inside and out.

Every small detail matters to us. The elegance of the framework, the simplicity and power of each line of code, the design and emotion behind every element. It all matters.

We really hope you love TypeRocket and as a result love WordPress even more.

Thank you,

Kevin

### Core Values

The TypeRocket project operates by the belief that:

1. Software development should be friendly for designers.
2. You should be in total control of how content is managed, experienced and displayed.
3. Managing content should be fun, easy and powerful.
4. Beauty should never be forgotten.
5. Content and design shouldn't be dependant on third-party Plugins.
6. Consistency leads to happiness.
7. Give and go further.

Visit TypeRocket http://typerocket.com to get access to the tutorials and documentation.

### Icons

http://icomoon.io/#preview-free licensed under the GPL.


### TypeRocket Core Developers

When making updates to TypeRocket core asset files in coffee or sass add in the WP root dir.

Tools: Node, NPM, Gulp

- package.json

Use `npm install --dev`

```json
{
  "private": true,
  "devDependencies": {
    "gulp": "~3.9",
    "laravel-elixir-coffeescript": "^1.0.2"
  },
  "dependencies": {
    "laravel-elixir": "^6.0.0-9"
  }
}
```

- gulpfile.js

Update the `elixir.config.assetsPath` to your root typerocket folder location.

```js
var elixir = require('laravel-elixir');

elixir.config.assetsPath = './wp-content/themes/default/typerocket/assets';
elixir.config.sourcemaps = false;

elixir(function(mix) {
    var coffee_items = [
        'http.coffee',
        'booyah.coffee',
        'typerocket.coffee',
        'items.coffee',
        'media.coffee',
        'matrix.coffee',
        'builder.coffee',
        'link.coffee',
        'dev.coffee'
    ];

    mix.coffee( coffee_items , elixir.config.assetsPath + '/js/typerocket.js', 'coffee');
    mix.sass(['*.scss'], elixir.config.assetsPath + '/css/typerocket.css');
});
```
