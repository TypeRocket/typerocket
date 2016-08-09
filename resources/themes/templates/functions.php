<?php
tr_post_type('Book')->setIcon('book')->setTitleForm(function() {
    $form = tr_form()->setGroup('everything');
    echo $form->search('Search');
    echo $form->builder('Builder');
    echo $form->matrix('Builder')->setGroup('matrix.two');
    echo $form->row( $form->text('One'), $form->text('Two') );
    echo $form->textarea('Thee');
    echo $form->repeater('Four')->setFields([
        $form->text('Five')
    ]);
});

tr_meta_box('Details')->setCallback(function() {

    $form = tr_form();

    echo $form->text('Top');

    echo $form->row(
        $form->text('First Name'),
        $form->text('Last Name')
    );

    echo $form->setGroup('about')->row(
        $form->text('First Name'),
        $form->text('Last Name')
    );

    echo $form->text('Middle');

    echo $form->setGroup('thirds')->row(
        $form->text('First Name'),
        $form->text('Last Name')
    );

    echo $form->text('Bottom');

})->addPostType('book');

tr_resource_pages('Member');