<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Time extends Field {

    function __construct() {
	    $paths = \TypeRocket\Config::getPaths();
        wp_enqueue_script( 'jquery-ui-slider', array( 'jquery' ) );
        wp_enqueue_style( 'tr-time-picker-style', $paths['urls']['assets'] . '/css/time-picker.css' );
        wp_enqueue_script( 'tr-time-picker-script', $paths['urls']['assets'] . '/js/time-picker.js', array( 'jquery', 'jquery-ui-slider' ), '1.0', true );
        $this->type = 'text';
    }

    function render() {
        $name = $this->attr['name'];
        $value = esc_attr($this->get_value());

        if(isset($this->attr['class'])) {
            $this->attr['class'] .= ' time-picker';
        } else {
            $this->attr['class'] = ' time-picker';
        }

        unset($this->attr['name']);
        return Html::input($this->type, $name, $value, $this->attr);
    }

}