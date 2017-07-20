<?php
if ( ! function_exists( 'add_action' )) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
} ?>

<h1>Theme Options</h1>

<?php
$form = new \TypeRocket\Elements\Form();
$form->useJson();
$form->setGroup( $this->getName() );
?>

<div class="typerocket-container">
    <?php
    echo $form->open();

    // about
    $about = function() use ($form) {
        echo $form->text('Company Name');
        echo $form->text('Company Email');
        echo $form->text('Company Phone');
        echo $form->search('Terms Page')->setPostType('page');
        echo $form->checkbox('Company Open')->setText('Company open for business')->setLabel(false);
    };

    // api
    $api = function() use ($form) {
        $help = '<a target="blank" href="https://developers.googl..com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a>.';
        echo $form->password( 'Google Maps API Key')
                  ->setHelp($help)
                  ->setAttribute('autocomplete', 'new-password');
    };

    // save
    $save = $form->submit( 'Save' );

    // layout
    tr_tabs()->setSidebar( $save )
    ->addTab( 'About', $about )
    ->addTab( 'APIs', $api )
    ->render( 'box' );
    echo $form->close();
    ?>

</div>
