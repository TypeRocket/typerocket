<?php
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
} ?>

<h2>Theme Options</h2>

<?php
$form = new \TypeRocket\Form();
$form->setId($this->getName());
$form->setGroup($this->getName());
$form->setAction('update');
$form->setup();
?>

<div id="tr-dev-content" class="typerocket-container">
    <?php

    $form->open();
    $utility = new \TypeRocket\Buffer();

    // about
    $utility->startBuffer();
    $form->text('Company Name');
    $contact = array(
        array('text', array('Phone')),
        array('text', array('Email')),
    );

    $form->renderFields($contact);
    $utility->indexBuffer('about');

    // api
    $utility->startBuffer();
    $help = '<a target="blank" href="https://developers.google.com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a> to activate maps in the theme.';
    $form->text('Google Maps API Key', array(), array('help' => $help));
    $utility->indexBuffer('api');

    // save
    $utility->startBuffer();
    $form->setDebugStatus(false);
    $form->submit('Save');
    $utility->indexBuffer('save');

    // layout
    $tabs = new TypeRocket\Tabs();
    $tabs->setSidebar($utility->getBuffer('save'));
    $tabs->addTab( array(
      'id' => 'about',
      'title' => 'About',
      'content' => $utility->getBuffer('about')
    ) );
    $tabs->addTab( array(
      'id' => 'advanced',
      'title' => 'APIs',
      'content' => $utility->getBuffer('api')
    ) );
    $tabs->render('boxed');
    $form->close();
  ?>

</div>
