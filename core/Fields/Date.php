<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Date extends Field {

	function __construct() {
		$paths = \TypeRocket\Config::getPaths();
		wp_enqueue_style( 'tr-date-picker', $paths['urls']['assets'] . '/css/date-picker.css' );
		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
		$this->type = 'text';
	}

	function render() {
		$name  = $this->attr['name'];
		$value = esc_attr( $this->getValue() );

		if ( isset( $this->attr['class'] ) ) {
			$this->attr['class'] .= ' date-picker';
		} else {
			$this->attr['class'] = ' date-picker';
		}

		unset( $this->attr['name'] );

		return Html::input( $this->type, $name, $value, $this->attr );
	}

}