<?php
if ( ! function_exists( 'add_action' )) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
} ?>

<h2>Theme Options</h2>

<?php
$form = new \TypeRocket\Form();
$form->setGroup( $this->getName() );
$form->setAction( 'update' );
$form->setup();
?>

<div id="tr-dev-content" class="typerocket-container">
    <?php

    $form->open();
    $buffer = tr_buffer();

    // about
    $contact = array(
        array( 'text', array( 'Company Name' ) ),
        array( 'text', array( 'Phone' ) ),
        array( 'text', array( 'Email' ) ),
    );
    $about = $form->getFromFieldsString( $contact );

    // api
    $help = '<a target="blank" href="https://developers.google.com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a> to activate maps in the theme.';
    $api = $form->text( 'Google Maps API Key')->setSetting('help', $help);

    // save
    $form->setDebugStatus( false );
    $save = $form->submit( 'Save' );

    // layout
    tr_tabs()->setSidebar( $save )
    ->addTab( 'About', $about )
    ->addTab( 'APIs', $api )
    ->render( 'box' );
    $form->close();
    ?>

</div>
