## TypeRocket WordPress Framework

[![Total Downloads](https://poser.pugx.org/TypeRocket/typerocket/d/total.svg)](https://packagist.org/packages/TypeRocket/typerocket)

TypeRocket is a powerful framework for WordPress developers. We set out to make a framework designed to be beautiful within WordPress and deliver the tools needed to build a modern website or application.
  
TypeRocket makes it easy to do so much. Build component based designs. Add post types, taxonomies, meta boxes, pages, forms and fields. Create custom routes, models, controller, middleware and views.

## Documentation

Documentation and examples can be found at [http://typerocket.com](http://typerocket.com)

## License

TypeRocket is open-sourced software licenced under the [GNU General Public License 3.0](https://www.gnu.org/licenses/gpl-3.0.en.html)

## Quick Examples

The short list. Let the code speak for itself. (WordPress hooks not required)

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

### Front-end and MVC

Build custom apps.

#### Views

```php
// functons.php
tr_frontend();
```
Template,

```php
<?php get_header(); ?>
    <main class="content">
        <?php
        $form = tr_form('marketing', 'create');
        $list = 14829;
        $form->useUrl('POST', '/marketing/pdf/' . $list );
        echo $form->open();
        echo $form->text('Email')->setType('email');
        echo $form->close('Subscribe');
        ?>
    </main>
<?php get_footer(); ?>
```

#### Custom Routes

```php
tr_route()->get('/marketing', 'show@Marketing');
tr_route()->post('/marketing/{list}', 'subscribe@Marketing');
tr_route()->get('/marketing/thanks', 'thanks@Marketing');
```

#### Controllers

```php
<?php
namespace App\Controllers;

use \TypeRocket\Controllers\Controller;

class MarketingController extends Controller
{

    protected function routing()
    {
        $public = [ 'only' => [ 'show', 'subscribe', 'thanks' ] ];
        $this->setMiddleware('marketing', $public );
    }
    
    public function show()
    {
        return tr_view('marketing.show');
    }

    public function subscribe($list)
    {
        $data = $this->request->getDataPost();

        $mail = new \MailChimp;
        $mail->subscribeMemberToList($list, $data['email']);
        
        return tr_redirect()->toUrl('/marketing/thanks');
    }

    public function thanks()
    {
        return tr_view('marketing.thanks');
    }

}
```