## TypeRocket WordPress Framework

TypeRocket is like Advanced Custom Fields + Laravel + Magic in WordPress for FREE.

[http://typerocket.com](http://typerocket.com)

## Key Features

TypeRocket gives you extendable and modern tools to build anything you want on WordPress.

- Build component based designs
- Register post types
- Register taxonomies
- Create meta boxes
- Add pages
- Use forms and fields
- Use models, controllers, and views
- Register custom routes and middleware
- The list goes on...

## Quick Examples

Let the code speak for itself. (WordPress hooks not required)

### Post Type

```php
// Register Post Type
$person = tr_post_type('Person');

// Chain Methods with Eloquence
$person->setIcon('users')
       ->setTitlePlaceholder( 'Enter full name here' )
       ->setArchivePostsPerPage(-1);
       ->setTitleForm( function() {
           $form = tr_form();
           echo $form->image('Photo');
           echo $editor->text('Company');
           echo $editor->textarea('About Person');
       });

// Add Sortable Columns to Admin Index View
$person->addColumn('company', true);

// REST API
$person->setRest('person');
```

### Repeater Field

```php
$form = tr_form();

// Basic
echo $form->repeater('Speakers')->setFields([
    $form->image('Photo'),
    $form->text('Name'),
    $form->text('Slides URL')
]);

// With Layout Tabs
$tabs = tr_tabs()->bindCallbacks();

$tabs->addTab('Content')
     ->setTabFields('Content', [
         $form->textarea('Quote', ['maxlength' => 200]),
         $form->row(
             $form->text('First Name'),
             $form->text('Last Name')
         )
     ]);

$tabs->addTab('Images')
     ->setTabFields('Images', [
         $form->image('Avatar'),
         $form->gallery('Gallery'),
     ]);

echo $form->repeater('Stories')
    ->setFields([$tabs])
    ->setHeadline('Story');
```

## License

TypeRocket is open-sourced software licenced under the [GNU General Public License 3.0](https://www.gnu.org/licenses/gpl-3.0.en.html)