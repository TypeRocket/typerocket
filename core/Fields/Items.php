<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Items extends Field {

	function __construct() {
		$paths = \TypeRocket\Config::getPaths();
		wp_enqueue_script( 'typerocket-items-list', $paths['urls']['assets'] . '/js/items-list.js', array( 'jquery' ),
			'1.0', true );
	}

	function render() {
		$name                = $this->attr['name'];
		$this->attr['class'] = 'items-list';
		$items               = $this->get_value();
		unset( $this->attr['name'] );

		if ( empty( $this->settings['button'] ) ) {
			$this->settings['button'] = 'Insert Item';
		}

		$list = '';

		if ( is_array( $items ) ) {
			foreach ( $items as $value ) {
				$input = Html::input( 'text', $name . '[]', esc_attr( $value ) );

				$list .= Html::element( 'li', array(
					'class' => 'item'
				),
					'<div class="move tr-icon-menu"></div><a href="#remove" class="tr-icon-remove2 remove" title="Remove Item"></a>' . $input );

			}
		}

		unset( $this->attr['id'] );
		$html = Html::input( 'hidden', $name, 'no', $this->attr );
		$html .= '<div class="button-group">';
		$html .= Html::element( 'input', array(
			'type'  => 'button',
			'class' => 'items-list-button button',
			'value' => $this->settings['button']
		) );
		$html .= Html::element( 'input', array(
			'type'  => 'button',
			'class' => 'items-list-clear button',
			'value' => 'Clear'
		) );
		$html .= '</div>';

		if ( is_null( $name ) && is_string( $this->attr['data-name'] ) ) {
			$name = $this->attr['data-name'];
		}

		$html .= Html::element( 'ul', array(
			'data-name' => $name,
			'class'     => 'tr-items-list cf'
		), $list );

		return $html;
	}

}