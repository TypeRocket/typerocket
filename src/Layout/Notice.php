<?php

namespace TypeRocket\Layout;

class Notice
{
    /**
     *  Flash dismissible notice
     *
     * Notice can be closed
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

    /**
     *  Flash permanent notice
     *
     *  Notice can not be closed
     *
     * @param $data
     */
    public static function permanent( $data )
    {
        $classes = 'notice-' . $data['type'];
        if( !empty($data) ) {
            ?>
            <div class="notice tr-admin-notice <?php echo $classes; ?>">
                <p><?php echo $data['message']; ?></p>
            </div>
            <?php
        }
    }
}