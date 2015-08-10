## TypeRocket 2
Coding advanced WordPress themes became a blast. Be good looking and do cool stuff with less code.

http://typerocket.com

### Requirements

- WordPress 4.0+
- PHP 5.3+

### Language

- English only

### What can you do with TypeRocket?

- You can make make post types, taxonomies and meta boxes with one line of code.
- Add fields to post types, comments, meta boxes, option pages, user profiles and even on the front-end inside theme template files.
- You can access a number of pre-made TypeRocket plugins for features like SEO and Theme Options installed directly into your theme.

### Documentation

https://typerocket.com/docs/

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

### Fillable Fields Example

By default TypeRocket will save any and all fields. This is just fine in many cases and makes life easy. When you need to filter, validate or fill only specific fields we have you covered.

Set the fields for the "Book" post type the only fillable fields to make things a little more secure. You can do this by creating a book controller and model inside the app folder.

`BooksModel.php` is the main file we care about.

```php
<?php // /typerocket/app/Models/BooksModel.php
namespace TypeRocket\Models;

class BooksModel extends PostTypesModel
{
    protected $fillable = array(
        'book_cover',
        'isbn_number'
    );
}
```

`BooksController.php` needs to be created to handle the actions. Blank works just fine.

```php
<?php // /typerocket/app/Controllers/BooksController.php
namespace TypeRocket\Controllers;

class BooksController extends PostTypesController
{
}
```

Now only "ISBN Number" and "Book Cover" will be saved. However, because the resource controller/model has been specified, "books", only an administrator can manage the new fields.

### XKernel

You will need to create a new Kernel class called XKernel to specify the middleware you want to use. Middleware can be used for all kinds of fun things. In this case we want "books" to authenticate like posts.

`XKernel.php` manages the middleware for the resource and request types.

```php
<?php // /typerocket/app/Http/XKernel.php
namespace TypeRocket\Http;

class XKernel extends Kernel
{

    protected $middleware = array(
        'hookGlobal' =>
            array('AuthRead'),
        'restGlobal' =>
            array(
                'AuthRead',
                'ValidateCsrf'
            ),
        'noResource' => array(
            'AuthAdmin'
        ),
        'books' => // new resource middleware
            array('OwnsPostOrCanEditPosts'),
        'users' =>
            array('IsUserOrCanEditUsers'),
        'posts' =>
            array('OwnsPostOrCanEditPosts'),
        'pages' =>
            array('OwnsPostOrCanEditPosts'),
        'comments' =>
            array('OwnsCommentOrCanEditComments'),
        'options' =>
            array('CanManageOptions')
    );
}

```

### Filter Fields Example

If we want to filter the fields we can do this as well. Lets use the controller filter to sanitize the data being saved.

- Make sure the "ISBN Number" doesn't contain invalid characters
- Cast the "Book Cover" to an integer since we reference it by the attachment ID.

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
7. We should give and go further.

Visit TypeRocket http://typerocket.com to get access to the tutorials and documentation.

### Icons

http://icomoon.io/#preview-free licensed under the GPL.