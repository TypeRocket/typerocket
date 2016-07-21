<?php

namespace TypeRocket\Layout;

class Notice
{

    /**
     *  Flash notice
     *
     * @param $data
     */
    public static function dismissible( $data )
    {
        $classes = 'notice-' . $data['type'];
        if( !empty($data) ) {
            ?>
            <div class="notice tr-admin-notice <?php echo $classes; ?> is-dismissible">
                <p><?php echo $data['message']; ?></p>
            </div>
            <?php
        }
    }
}