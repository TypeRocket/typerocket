<?php
class DevPlugin
{

    public function __construct()
    {
        if ( ! function_exists( 'add_action' )) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }
    }

    public function setup()
    {
        add_filter( 'admin_footer_text', [$this, 'tr_remove_footer_admin']);
        $settings = [
            'view_file' => __DIR__ . '/page.php',
            'menu' => 'Dev'
        ];
        (new \TypeRocket\Register\Page('TypeRocket', __('Dev'), __('TypeRocket Developer Tools'), $settings))
            ->addToRegistry()->setIcon('bug');
    }

    public function tr_remove_footer_admin()
    {
        echo __('TypeRocket developer mode! Run time is ') . (TR_END - TR_START);
    }

}

add_action( 'typerocket_loaded', [new DevPlugin(), 'setup']);