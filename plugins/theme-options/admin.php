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
    $contact = [
        $form->text('Company Name'),
        $form->text('Company Email'),
        $form->text('Company Phone'),
        $form->search('Terms Page')->setPostType('page'),
        $form->checkbox('Company Open')->setText('Company open for business')->setLabel(false)
    ];
    $about = $form->getFromFieldsString( $contact );

    // api
    $help = '<a target="blank" href="https://developers.google.com/maps/documentation/embed/guide#api_key">Get Your Google Maps API</a> to activate maps in the theme.';
    $api = $form->password( 'Google Maps API Key')->setHelp($help)->setAttribute('autocomplete', 'new-password');

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
