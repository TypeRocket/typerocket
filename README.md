[![Build Status](https://travis-ci.org/TypeRocket/core.svg?branch=master)](https://travis-ci.org/TypeRocket/core) [![Total Downloads](https://poser.pugx.org/TypeRocket/core/d/total.svg)](https://packagist.org/packages/TypeRocket/core) [![Latest Stable Version](https://poser.pugx.org/TypeRocket/core/v/stable.svg)](https://packagist.org/packages/TypeRocket/core)


# TypeRocket WordPress Framework

TypeRocket is a modern MVC and interface framework designed to empower WordPress developers. TypeRocket is like Advanced Custom Fields + Laravel MVC + Custom Post Type UI + Magic for WordPress. 

Do more while writing less code, boost your WordPress sites performance, and craft elegant admin interfaces. 

[http://typerocket.com](http://typerocket.com)

## Highlighted Features

TypeRocket highly integrated into WordPress giving you and your team modern tools to build anything you want on WordPress.

- Advanced custom post types, taxonomies, and pages.
- Custom meta boxes, forms, and 20+ input fields.
- MVC with custom routing plus middleware.
- DI Container.
- Galaxy CLI.
- Composer based TypeRocket plugin support and publishing.
- Integrated WordPress ORM with advanced features like eager loading.
- Database migrations.
- Custom theme and plugin WordPress automatic updates API integrations.
- Gutenberg support.
- Configurable features like lazy image resizing and complete removal of comments.
- You control your templating system, Twig or Laravel Blade.
- The list goes on...

### Advanced Custom Post Types

Fully customize your custom post types with less code and no need for WordPress action and filter hooks.   

```php
tr_post_type('Person')
    ->setIcon('users')
    ->forceDisableGutenberg()
    ->setTitlePlaceholder( 'Enter full name here' )
    ->setArchivePostsPerPage(-1);
    ->setTitleForm( function() {
        $form = tr_form();
        echo $form->image('Photo');
        echo $editor->text('Company');
        echo $editor->textarea('About Person');
    })
    ->addColumn('company');
```

### Repeater Fields

Add repeaters to your WordPress admin or front-end.  

```php
$form = tr_form();
echo $form->repeater('Speakers')->setFields([
    $form->image('Photo'),
    $form->row([
        $form->text('Given Name'),
        $form->text('Last Name')
    ])
]);
```

### WordPress Integrated Advanced ORM

Create your own models and dramatically improve your sites performance using eager loading that is integrated with the WordPress object cache.

```php
<?php
$symbol_model = new \App\Models\Symbol;
$symbol_model->with('meta')->published()->whereMeta('feature', '=', '1')->get();
```

Define relationships between models using familiar Laravel ORM eloquence.

```php
<?php
namespace App\Models;

use TypeRocket\Models\WPPost;

class Post extends WPPost
{
    protected $postType = 'post';

    public function categories()
    {
        return $this->belongsToTaxonomy(Category::class, 'category');
    }

    public function tags()
    {
        return $this->belongsToTaxonomy(Tag::class, 'post_tag');
    }

}
```

### Routing 

Create your application using MVC with custom routing and no need hack around WordPress rewrite rules.

```php
tr_route()->put()->match('/profile/([^\/]+)', ['id']))->do('update@Member');
tr_route()->get()->match('/profile/([^\/]+)', ['id']))->do('profile@Member');
```

```php
class MemberController extends Controller
{
    public function profile( $id ) {
        return tr_view('profile.show', ['id' => $id]);
    }

    public function update( $id, \App\Models\Member $member ) {
        $member->name = $this->request->getFields('name');
        $member->save();
        $this->response->flashNext('Profile updated!');
        return tr_redirect()->back();
    }
}
```

Or, quickly create a JSON API by simply returning a model or collection as your response.

```php
tr_route()->get()->match('posts')->do(function() {
    return (new App\Models\Post)->with('meta')->published()->get();
});
```

## License

TypeRocket is open-sourced software licenced under the [GNU General Public License 3.0](https://www.gnu.org/licenses/gpl-3.0.en.html)
