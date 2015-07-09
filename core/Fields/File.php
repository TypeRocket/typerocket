<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;
use \TypeRocket\Config as Config;

class File extends Field {

	function __construct() {
		$paths = Config::getPaths();
		wp_enqueue_media();
		wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
			true );
	}

	function render() {
		$name                = $this->attr['name'];
		$this->attr['class'] = 'file-picker';
		$value               = esc_attr( $this->getValue() );
		unset( $this->attr['name'] );

		if ( empty( $this->settings['button'] ) ) {
			$this->settings['button'] = 'Insert File';
		}

		if ( $value != "" ) {
			$url  = wp_get_attachment_url( $value );
			$file = '<a target="_blank" href="' . $url . '">' . $url . '</a>';
		} else {
			$file = '';
		}

		$html = Html::input( 'hidden', $name, $value, $this->attr );
		$html .= '<div class="button-group">';
		$html .= Html::element( 'input', array(
			'type'  => 'button',
			'class' => 'file-picker-button button',
			'value' => $this->settings['button']
		) );
		$html .= Html::element( 'input', array(
			'type'  => 'button',
			'class' => 'file-picker-clear button',
			'value' => 'Clear'
		) );
		$html .= '</div>';
		$html .= Html::element( 'div', array(
			'class' => 'file-picker-placeholder'
		), $file );

		return $html;
	}

}