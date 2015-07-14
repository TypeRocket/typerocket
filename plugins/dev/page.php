<?php

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

function tr_dev_icons()
{

    $icons = new TypeRocket\Icons;

    $generator = new \TypeRocket\Html\Generator();

    echo '<h3><i class="tr-icon-tools"></i> Icons</h3><p>These can be used with custom post types.</p><ol>';
    foreach ($icons as $k => $v) {
        echo $generator->newElement( 'li', array( 'class' => 'tr-icon-' . $k ),
            ' ' . $k . ' ( .tr-icon-' . $k . ' )' )->getString();
    }
    echo '</ol>';

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
