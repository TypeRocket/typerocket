## TypeRocket

[![Total Downloads](https://poser.pugx.org/TypeRocket/typerocket/d/total.svg)](https://packagist.org/packages/TypeRocket/typerocket)

The WordPress Framework Designed for Developers.

[http://typerocket.com](http://typerocket.com)

- Register post types, taxonomies and meta boxes with one line of code.
- Add fields to post types, comments, meta boxes, option pages, user profiles... everywhere.
- Create custom admin pages, models, controllers, middleware, views and routes.
- And so much more...

## Install Composer Dependencies

Install packages using [Composer](https://getcomposer.org/).

```
composer install
```

## Code Example

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

### Requirements

- WordPress 4.5+
- PHP 5.5+

### Languages

- English only

### Icons

http://icomoon.io/#preview-free licensed under the GPL.