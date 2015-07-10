<?php
function tr_dev_icons()
{

    $icons = TypeRocket\Icons::$icon;

    $genorator = new \TypeRocket\Html\Generator();

    echo '<h3><i class="tr-icon-tools"></i> Icons</h3><p>These can be used with custom post types.</p><ol>';
    foreach ($icons as $k => $v) {
        echo $genorator->newElement( 'li', array( 'class' => 'tr-icon-' . $k ),
            ' ' . $k . ' ( .tr-icon-' . $k . ' )' )->getString();
    }
    echo '</ol>';

}

function tr_dev_forms()
{

    $fields = array(
        'post_title',
        'post_content',
        'post_type',
        'post_status',
        'post_author',
        'post_excerpt',
        'comment_status'
    );

    echo '<h3><i class="tr-icon-pencil"></i> Form Building</h3><p>These are some of the field names for posts in the WordPress database. Helping when you need to make front-end forms to <a href="http://codex.wordpress.org/Function_Reference/wp_insert_post" target="_blank">create</a> or <a href="http://codex.wordpress.org/Function_Reference/wp_update_post" target="_blank">update</a> posts. TypeRocket will add underscores and lowercase field names for you. When updating a user try the field names at <a href="http://codex.wordpress.org/Function_Reference/wp_update_user" target="_blank">http://codex.wordpress.org/Function_Reference/wp_update_user</a></p><ul>';
    foreach ($fields as $v) {
        echo \TypeRocket\Html::element( 'li', array(), $v );
    }
    echo '</ul>';

    ?>
    <h4>Updating Post Example (if on the single page)</h4>
    <pre><code>$form = new tr_form();
            $form->make('post', 'update', $post->ID);
            $form->process();
            $form->open();
            $form->text('Post Title', array(), array('builtin' => true));
            $form->textarea('Post Content', array(), array('builtin' => true));
            $form->close('Update');</code></pre>
    <h4>Creating Post Example</h4>
    <pre><code>$form = new tr_form();
            $form->make('post', 'create');
            $form->create_statics = array(
            'post_status' => 'publish',
            'post_type' => 'post'
            );
            $form->process();
            $form->open();
            $form->text('Post Title', array(), array('builtin' => true));
            $form->textarea('Post Content', array(), array('builtin' => true));
            $form->close('Create');</code></pre>
    <h4>Updating Current User Example</h4>
    <pre><code>$form = new tr_form();
            $form->make('user', 'update', get_current_user_id());
            $form->process();
            $form->open();
            $form->text('Last Name', array(), array('builtin' => true ));
            $form->text('First Name', array(), array('builtin' => true ));
            $form->close('Update Your Last Name');</code></pre>
    <?php

}

function tr_dev_stats()
{ ?>

    <h3><i class="tr-icon-pie"></i> Run Time</h3>
    <p>If you are using xDebug profiling or tracking times will be slower.</p>
    <p><b>TR Run Time</b>: <?php echo TR_END - TR_START; ?></p>


<?php } ?>

<div class="wrap">
    <h2>TypeRocket Dev</h2>

    <div>
        <div id="tr-dev-content" class="typerocket-container">
            <?php
            $screen = new \TypeRocket\Layout();
            $screen->add_tab( array(
                'id'       => 'stats',
                'title'    => 'Stats',
                'content'  => '',
                'callback' => 'tr_dev_stats'
            ) )->add_tab( array(
                'id'       => 'icons',
                'title'    => 'Icon Names',
                'content'  => '',
                'callback' => 'tr_dev_icons'
            ) )->make(); ?>
        </div>
    </div>

</div>
