<?php
class tr_matrix extends tr_base {

	public $name = null;
	public $form = null;

	function __construct($name, &$form) {

		$this->name = (string) $name;
		$this->form = $form;

		wp_enqueue_script('tr_matrix', tr::$paths['urls']['plugins'] . '/matrix/js.js', array( 'jquery' ), true);
		wp_localize_script('tr_matrix', 'tr_matrix_url', tr::$paths['urls']['plugins'] . '/matrix');
		wp_enqueue_style( 'matrix-style', tr::$paths['urls']['plugins'] . '/matrix/css.css' );
	}

	function add() {

		$dir = __DIR__ . '/' . $this->name;
		$files = scandir($dir);

		$select =  "<select class=\"matrix-select-{$this->name}\">";

		foreach($files as $f) {
			if( $f != '.' && $f != '..' && file_exists($dir.'/'.$f)) {
				$path = pathinfo($f);
				$select .= tr_html::element( 'option', array( 'value' => $f ), $path['filename'] );
			}
		}

		$select .= '</select>';

		echo "
<div class='matrix-group control-section'>
<div class='matrix-controls control'>
{$select}
<div class='button matrix-button' data-id='{$this->name}'>Add {$this->name}</div>
</div>
<div class='matrix-fields matrix-fields-{$this->name}'>";
		$this->get();
		echo "</div></div>";

	}

	function get() {


		$val = (new tr_get_field())->value("[{$this->name}]", $this->form->item_id, $this->form->controller, false);

		if(is_array($val)) {

			foreach($val as $t => $i) {
				foreach($i as $type => $v) {
					echo '<div class="matrix-field-group">';
					include(__DIR__ . "/groups/{$type}.php");
					echo '</div>';
				}
			}

		}

	}

}