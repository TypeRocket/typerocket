<?php
if ( ! function_exists( 'add_action' )) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
} ?>

<h2>Theme Options</h2>

<?php
$form = new \TypeRocket\Form();
$form->setGroup( $this->getName() );
?>

<div id="tr-dev-content" class="typerocket-container">
    <?php
    echo $form->open();

    // about
    $contact = array(
        $form->text('Company Name'),
        $form->text('Company Email'),
        $form->text('Company Phone')
    );
    $about = $form->getFromFieldsString( $contact );

    // api
    $help = '<a target="blank" href="https://developers.google.com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a> to activate maps in the theme.';
    $api = $form->password( 'Google Maps API Key')->setSetting('help', $help)->setAttribute('autocomplete', 'new-password');

    // save
    $form->setDebugStatus( false );
    $save = $form->submit( 'Save' );

    // layout
    tr_tabs()->setSidebar( $save )
    ->addTab( 'About', $about )
    ->addTab( 'APIs', $api )
    ->render( 'box' );
    echo $form->close();
    ?>

</div>
