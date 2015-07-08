<h2>Theme Options</h2>

<?php
$form = new \TypeRocket\Form();
$form->id = $this->name;
$form->group = '['.$this->name.']';
$form->make();
$form->process();
$form->flash();
?>

<div id="tr-dev-content" class="typerocket-container">
    <?php

    $form->open();
    $utility = new \TypeRocket\Utility();

    // about
    $utility->buffer();

    $form->text('Company Name');

    $contact = array(
        array('text', array('Phone')),
        array('text', array('Email')),
        array('text', array('Hours of Service'))
    );

    $form->render_fields($contact);
    $utility->buffer('about');

    // api
    $utility->buffer();
    $form->text('Google Maps API Key', array(), array('help' => '<a target="blank" href="https://developers.google.com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a> to activate maps in the theme.'));
    $utility->buffer('api');

    // save
    $utility->buffer();
    $form->submit('Save');
    $utility->buffer('save');

    // layout
    $screen = new TypeRocket\Layout();
    $screen->set_sidebar($utility->buffer['save']);
    $screen->add_tab( array(
      'id' => 'about',
      'title' => 'About',
      'content' => $utility->buffer['about']
    ) );
    $screen->add_tab( array(
      'id' => 'advanced',
      'title' => 'APIs',
      'content' => $utility->buffer['api']
    ) );
    $screen->make();
    $form->close();
  ?>

</div>


