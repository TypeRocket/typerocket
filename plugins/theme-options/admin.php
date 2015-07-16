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
    $buffer->startBuffer();
    $form->text( 'Company Name' );
    $contact = array(
        array( 'text', array( 'Phone' ) ),
        array( 'text', array( 'Email' ) ),
    );

    $form->renderFields( $contact );
    $buffer->indexBuffer( 'about' );

    // api
    $buffer->startBuffer();
    $help = '<a target="blank" href="https://developers.google.com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a> to activate maps in the theme.';
    $form->text( 'Google Maps API Key', array(), array( 'help' => $help ) );
    $buffer->indexBuffer( 'api' );

    // save
    $buffer->startBuffer();
    $form->setDebugStatus( false );
    $form->submit( 'Save' );
    $buffer->indexBuffer( 'save' );

    // layout
    tr_tabs()->setSidebar( $buffer->getBuffer( 'save' ) )
    ->addTab( 'About', $buffer->getBuffer( 'about' ) )
    ->addTab( 'APIs', $buffer->getBuffer( 'api' ) )
    ->render( 'box' );
    $form->close();
    ?>

</div>
