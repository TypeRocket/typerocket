## TypeRocket

[![Total Downloads](https://poser.pugx.org/TypeRocket/typerocket/d/total.svg)](https://packagist.org/packages/TypeRocket/typerocket)

The WordPress Framework Designed for Developers.

[http://typerocket.com](http://typerocket.com)

- Register post types, taxonomies and meta boxes with one line of code.
- Add fields to post types, comments, meta boxes, option pages, user profiles... everywhere.
- Create custom admin pages, models, controllers, middleware, views and routes.
- And so much more...

## Install Composer Dependencies

To install TypeRocket use [composer](https://getcomposer.org/). Go to your theme folder in the command line and run the composer command:

```sh
composer create-project --prefer-dist typerocket/typerocket
```

At the top of your themes `functions.php` file require `typerocket/init.php`. This will initialize TypeRocket.

```php
<?php // functions.php

require ('typerocket/init.php');
```

## Code Example

Touch the tip of the iceberg. This short snippet of code creates custom fields, saves the data and registers a custom post type. 

```php
<?php // functions.php
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