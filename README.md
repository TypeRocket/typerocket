# TypeRocket

TypeRocket is a highly integrated WordPress framework designed for the modern developer. TypeRocket brings into one place the advanced features of ACF, Laravel, Custom Post Type UI, and more. 

Enjoy a fluent syntax, refined UI, and powerfully fast APIs... [TypeRocket](http://typerocket.com).

## Modern Features

Build applications in less time with less maintenance.

- Advanced custom post types, taxonomies, and admin pages.
- Custom meta boxes, forms, fieldsets, and 29+ input fields.
- Integrate custom fields with external APIs.
- MVC, custom routing, and middleware.
- DI and Service Container.
- Galaxy CLI.
- Composer based extension publishing.
- Fully integrated WordPress ORM with advanced features like eager loading and object caching.
- Database migrations.
- Gutenberg support.
- Extensions: TypeRocket UI, Page Builder, and more.
- Advanced policy, capability, and role APIs.
- The list goes on...

### Advanced Pro Features

- Conditional fields and contexts.
- Automatic WordPress theme and plugin updates.
- Integrated WYSIWYG Redactor editor.
- Additional extensions: Theme Options, SEO Meta, Dev Tools Query Monitor, and more.
- Flexible template engines Tachyons and Twig.
- Hot swappable drivers for logging including: file, slack, and email.
- Hot swappable drivers for mailing including: log, wp, and mailgun.
- Hot swappable drivers for storage including: local storage (S3 coming soon).
- Additional form fields: background, swatch, editor, checkboxes, location, gallery, textexpand, range, and url.
- Advanced page builder and matrix field component system.
- Cloning for the repeater and component based fields.
- Conditional fields.
- Template routeing and controller to add MVC to your theme development.
- Additional CLI commands.
- Whoops PHP.
- Tables UI.
- Http helpers like: downloadables and async.
- And more...

### Advanced Custom Post Types

Fully customize your custom post types with less code and no need for WordPress action and filter hooks.   

```php
tr_post_type('Person')
    ->setIcon('users')
    ->forceDisableGutenberg()
    ->setTitlePlaceholder( 'Enter full name here' )
    ->setArchivePostsPerPage(-1)
    ->setTitleForm( function() {
        $form = tr_form();
        echo $form->image('Photo');
        echo $form->text('Company');
        echo $form->textarea('About Person');
    })
    ->addColumn('company');
```

### Repeater Fields

Add repeaters to your WordPress admin or front-end.  

```php
$form->repeater('Speakers')->setFields([
    $form->image('Photo'),
    $form->row(
        $form->text('Given Name'),
        $form->text('Last Name')
    )
]);
```

### Conditional Fields

Show fields or contexts only **when** a field's conditions are true.  

```php
echo $form->image('Photo');
echo $form->text('Alt Text')->when('photo');

echo $form->Toggle('Has Name');
echo $form->feildset('Full Name', 'Your identity information.',
    $form->text('Given Name'),
    $form->text('Last Name')
)->when('has_name');
```

### WordPress Integrated Advanced ORM

Craft your models and dramatically improve your site's performance using eager loading and integrated with the WordPress object cache.

```php
(new Post)->with('meta')->published()->whereMeta('featured', '=', '1')->get();
```

Define relationships between models using familiar Laravel ORM eloquence.

```php
class Post extends WPPost
{
    // ...

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

### MVC

Stop hacking the WordPress template and rewrite system. Create your application using MVC with custom routing.

```php
tr_route()->put()->on('/profile/*', 'update@Member');
tr_route()->get()->on('/profile/*', 'profile@Member');
```

Use authentication policies with your controllers and models, use views, flash notification messages to the admin, get classes from the service container, and more.

```php
class MemberController extends Controller
{
    public function profile(Member $member) {
        return tr_view('profile.show', compact('member'));
    }

    public function update(Member $member, AuthUser $user, Request $request ) {
        
        if(!$member->can('update', $user)) {
            tr_abort(401);
        }

        $member->save($request->getFields());

        tr_response()->flashNext('Profile updated!');

        return tr_redirect()->back();
    }
}
```

Or, quickly create a JSON API by merely returning a model or collection as your response.

```php
tr_route()->get()->on('posts', function() {
    return (new Post)->with('meta')->published()->get();
});
```

### Pro Templating

Dry up your templates using views and controllers with this Pro only feature. Views in admin and front-end.

```php
/**
 * Example WordPress Template MVC
 * 
 * your-theme/index.php
 *
 * @var WP_Post[] $posts
 */
tr_template_controller(function() use ($posts) {
    $button_class = tr_post_field('button_class');
    
    $classes = class_names('button',  [
        'button-primary' => $button_class == 'primary',
        'button-error' => $button_class == 'error',
    ]);
    
    return tr_view('index', compact('classes'));
});
```

## License

TypeRocket is open-sourced software licenced under the [GNU General Public License 3.0](https://www.gnu.org/licenses/gpl-3.0.en.html).