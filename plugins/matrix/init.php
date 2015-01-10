<?php
class tr_matrix extends tr_base {

	public $name = null;
	public $form = null;

	function __construct($name, &$form) {

		$this->name = (string) $name;
		$this->form = $form;

		wp_enqueue_script('tr_matrix', tr::$paths['urls']['plugins'] . '/matrix/js.js', array( 'jquery' ), true);
		wp_localize_script('tr_matrix', 'tr_matrix_url', tr::$paths['urls']['plugins'] . '/matrix');
		wp_localize_script('tr_matrix', 'tr_matrix_form_group', $form->group);
		wp_enqueue_style( 'matrix-style', tr::$paths['urls']['plugins'] . '/matrix/css.css' );
	}

	function add() {

		$dir = __DIR__ . '/' . $this->name;
		$files = scandir($dir);

		$select =  "<select class=\"matrix-select-{$this->name}\">";

		foreach($files as $f) {
			if( $f != '.' && $f != '..' && file_exists($dir.'/'.$f)) {
				$path = pathinfo($f);
				$select .= tr_html::element( 'option', array( 'value' => $f ), ucfirst($path['filename']) );
			}
		}

		$select .= '</select>';

		echo "
<div class='tr-matrix control-section tr-repeater'>
<div class='matrix-controls controls'>
{$select}
<div class=\"button-group\">
<input type=\"button\" value=\"Add {$this->name}\" data-id='{$this->name}' class=\"button matrix-button\">
<input type=\"button\" value=\"Flip\" class=\"flip button\">
<input type=\"button\" value=\"Clear All\" class=\"clear button\">
</div>
</div>
<div><input type='hidden' name='tr{$this->form->group}[{$this->name}]' /></div>
<div class='matrix-fields matrix-fields-{$this->name} tr-repeater-fields ui-sortable'>";
		$this->get();
		echo "</div></div>";

	}

	function get() {

		$val = (new tr_get_field())->value($this->form->group ."[{$this->name}]", $this->form->item_id, $this->form->controller, false);

		if(is_array($val)) {

			foreach($val as $t => $i) {
				foreach($i as $type => $v) {

					$form = $this->form;

					$form->debug = false;
					$tr_matrix_id = $t;
					$tr_matrix_group = $this->name;
					$tr_matrix_type = lcfirst($type);
					$init_grp = $form->group;

					$form->group = $init_grp . "[{$tr_matrix_group}][{$tr_matrix_id}][{$tr_matrix_type}]";

					echo '<div class="matrix-field-group tr-repeater-group">';
					include(__DIR__ . "/groups/{$type}.php");
					echo '</div></div>';

					$form->group = $init_grp;
					$form->debug = true;
				}
			}

		}

	}

}