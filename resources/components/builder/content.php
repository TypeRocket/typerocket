<h1>Content Component</h1>
<?php
/** @var $form \TypeRocket\Elements\Form */
echo $form->editor('Content');
echo $form->text('Headline');
echo $form->repeater('Repeater')->setFields([
    $form->text('First'), $form->text('Last')
]);