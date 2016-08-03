## TypeRocket

Code advanced WordPress sites quickly and easily with TypeRocket.

- You can make make post types, taxonomies and meta boxes with one line of code.
- Add fields to post types, comments, meta boxes, option pages, user profiles and even on the front-end inside theme template files with minimal effort.
- You can access a number of pre-made TypeRocket plugins for features like SEO and Theme Options installed directly into your theme.
- Add custom admin pages, models, controllers, middleware, views and routes.
- And so much more...

See [http://typerocket.com](http://typerocket.com)

### Code Example

Touch the tip of the iceberg. This short snippet of code creates custom fields, saves the data and registers a custom post type. 

```php
<?php // functions.php
include 'typerocket/init.php';

$book = tr_post_type('Book')->setIcon('Book');
$bookDetails = tr_meta_box('Book Details');
$bookDetails->setCallback( function() {
  $form = tr_form();
  echo $form->text('Author');
  echo $form->text('Publisher');
  echo $form->text('ISBN');
  echo $form->image('Book Cover');
});

$book->apply($bookDetails);
```

[View the Advanced Tutorial](https://typerocket.com/making-a-book-post-type-in-wordpress-with-typerocket/)

### Requirements

- WordPress 4.5+
- PHP 5.5+

### Languages

- English only

### Icons

http://icomoon.io/#preview-free licensed under the GPL.