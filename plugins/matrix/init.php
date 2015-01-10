<?php
class tr_matrix extends tr_base {

	public $name = null;
	public $form = null;
	public $settings = array();

	function __construct($name, &$form, $settings = array()) {

		$this->name = (string) $name;
		$this->form = $form;
		$this->settings = $settings;

		wp_enqueue_script( 'typerocket-booyah', tr::$paths['urls']['assets'] . '/js/booyah.js', array('jquery'), '1.0', true );
		wp_enqueue_script('jquery-ui-sortable', array( 'jquery' ), '1.0', true);
		wp_enqueue_style( 'tr-date-picker', tr::$paths['urls']['assets'] . '/css/date-picker.css' );
		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'typerocket-media', tr::$paths['urls']['assets'] . '/js/media.js', array('jquery'), '1.0', true );
		wp_enqueue_script( 'typerocket-items-list', tr::$paths['urls']['assets'] . '/js/items-list.js', array('jquery'), '1.0', true );

		wp_enqueue_script('tr_matrix', tr::$paths['urls']['plugins'] . '/matrix/js.js', array( 'jquery' ), true);
		wp_localize_script('tr_matrix', 'tr_matrix_url', tr::$paths['urls']['plugins'] . '/matrix');
		wp_localize_script('tr_matrix', 'tr_matrix_form_group', $form->group);
		wp_enqueue_style( 'matrix-style', tr::$paths['urls']['plugins'] . '/matrix/css.css' );

	}

	function add() {

		$dir = __DIR__ . '/' . $this->name;
		$files = scandir($dir);

		$mxid = md5(microtime(true));

		$select =  "<select class=\"matrix-select-{$mxid}\">";

		foreach($files as $f) {

			if( $f != '.' && $f != '..' && file_exists($dir.'/'.$f)) {
				$path = pathinfo($f);

				if(!empty($this->settings) && in_array($f, $this->settings)) {
					$select .= tr_html::element( 'option', array( 'value' => $f, 'data-file' => $path['filename']), $this->clean_file_name($path['filename']) );
				} elseif(empty($this->settings)) {
					$select .= tr_html::element( 'option', array( 'value' => $f, 'data-file' => $path['filename'] ), $this->clean_file_name($path['filename']) );
				}

			}

		}

		$select .= '</select>';

		echo "
<div class='tr-matrix control-section tr-repeater'>
<div class='matrix-controls controls'>
{$select}
<div class=\"button-group\">
<input type=\"button\" value=\"Add {$this->name}\" data-id='$mxid' data-folder='{$this->name}' class=\"button matrix-button\">
<input type=\"button\" value=\"Flip\" class=\"flip button\">
<input type=\"button\" value=\"Collapse\" class=\"tr_action_collapse button\">
<input type=\"button\" value=\"Clear All\" class=\"clear button\">
</div>
</div>
<div><input type='hidden' name='tr{$this->form->group}[{$this->name}]' /></div>
<div class='matrix-fields matrix-fields-$mxid tr-repeater-fields ui-sortable'>";
		$this->get();
		echo "</div></div>";

		return $this;
	}

	private function clean_file_name($name) {
		$name = str_replace('-', ' ', $name );
		str_replace('_', ' ', $name );
		return ucwords($name);
	}

	private function get() {

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