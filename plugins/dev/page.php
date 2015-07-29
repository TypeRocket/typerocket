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

    <h3>Run Time</h3>
    <p>If you are using xDebug profiling or tracking times will be slower.</p>
    <p><b>TR Run Time</b>: <?php echo TR_END - TR_START; ?></p>
    <p>Also noted in the footer at every page load when debug mode is on.</p>


<?php } ?>

<div class="wrap">
    <h2>TypeRocket Dev</h2>

    <div>
        <div id="tr-dev-content" class="typerocket-container">
            <?php
            $tabs = tr_tabs();
            $tabs->addTab( array(
                'id'       => 'stats',
                'title'    => 'Stats',
                'content'  => '',
                'callback' => 'tr_dev_stats'
            ) )->addTab( array(
                'id'       => 'icons',
                'title'    => 'Icon Names',
                'content'  => '',
                'callback' => 'tr_dev_icons'
            ) )->render('box'); ?>
        </div>
    </div>

</div>
