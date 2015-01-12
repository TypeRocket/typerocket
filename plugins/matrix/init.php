<?php
class tr_matrix {

	public $name = null;
	public $form = null;
	public $settings = array();
	public $include_files = array();
	public $label = null;

	function __construct($name, &$form, $settings = array(), $label = false, $include_files = array()) {

		$this->name = (string) $name;
		$this->form = $form;
		$this->include_files = $include_files;
		$this->settings = $settings;
		$this->label = $label;
		$this->mxid = md5(microtime(true)); // set id for matrix random
		// load everything :(
		wp_enqueue_script( 'typerocket-booyah', tr::$paths['urls']['assets'] . '/js/booyah.js', array('jquery'), '1.0', true );
		wp_enqueue_script('jquery-ui-sortable', array( 'jquery' ), '1.0', true);
		wp_enqueue_style( 'tr-date-picker', tr::$paths['urls']['assets'] . '/css/date-picker.css' );
		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'typerocket-media', tr::$paths['urls']['assets'] . '/js/media.js', array('jquery'), '1.0', true );
		wp_enqueue_script( 'typerocket-items-list', tr::$paths['urls']['assets'] . '/js/items-list.js', array('jquery'), '1.0', true );
		wp_enqueue_script( 'jquery-ui-slider', array( 'jquery' ) );
		wp_enqueue_style( 'tr-time-picker-style', tr::$paths['urls']['assets'] . '/css/time-picker.css' );
		wp_enqueue_script( 'tr-time-picker-script', tr::$paths['urls']['assets'] . '/js/time-picker.js', array( 'jquery', 'jquery-ui-slider' ), '1.0', true );

		// load just matrix :)
		wp_enqueue_script('tr_matrix', tr::$paths['urls']['plugins'] . '/matrix/js.js', array( 'jquery' ), true);
		wp_localize_script('tr_matrix', 'tr_matrix_url', tr::$paths['urls']['plugins'] . '/matrix');
		wp_localize_script('tr_matrix', 'tr_matrix_form_group', $form->group);

	}

	function add() {

		// setup select list of files
		$select = $this->get_select_html();
		$debug = $this->get_debug_html();
		$help = $this->get_help_html();
		$label = $this->get_label_html();

		// add it all
		echo "
<div class='tr-matrix control-section tr-repeater'>
{$debug}
<div class='matrix-controls controls'>
{$label}
{$select}
<div class=\"button-group\">
<input type=\"button\" value=\"Add New\" data-id='{$this->mxid}' data-folder='{$this->name}' class=\"button matrix-button\">
<input type=\"button\" value=\"Flip\" class=\"flip button\">
<input type=\"button\" value=\"Collapse\" class=\"tr_action_collapse button\">
<input type=\"button\" value=\"Clear All\" class=\"clear button\">
</div>
{$help}
</div>
<div><input type='hidden' name='tr{$this->form->group}[{$this->name}]' /></div>
<div class='matrix-fields matrix-fields-{$this->mxid} tr-repeater-fields ui-sortable'>";
		$this->get_from();
		echo "</div></div>";

		return $this;
	}

	private function clean_file_name($name) {
		$name = str_replace('-', ' ', $name );
		str_replace('_', ' ', $name );
		return ucwords($name);
	}

	private function get_help_html() {
		if(isset($this->settings['help'])) {
			$help =
				"<div class=\"help\">
          <p>{$this->settings['help']}</p>
        </div>";
		} else {
			$help = '';
		}

		return $help;
	}

	private function get_label_html() {
		if(is_string($this->label)) {
			$label = "<div class=\"control-label\"><span class=\"label\">{$this->label}</span></div>";
		} else {
			$label = '';
		}

		return $label;
	}

	private function get_debug_html() {
		$debug = '';
		if(TR_DEBUG === true && is_admin()) {
			$debug =
				"<div class=\"dev\">
        <span class=\"debug\"><i class=\"tr-icon-bug\"></i></span>
          <span class=\"nav\">
          <span class=\"field\">
            <i class=\"tr-icon-code\"></i><span>tr_{$this->form->controller}_field(\"{$this->form->group}[{$this->name}]\");</span>
          </span>
        </span>
      </div>";
		}

		return $debug;
	}

	private function get_select_html() {

		$dir = __DIR__ . '/' . $this->name;

		if(file_exists($dir)) {

			$files = preg_grep('/^([^.])/', scandir($dir));

			$select =  "<select class=\"matrix-select-{$this->mxid}\">";

			foreach($files as $f) {
				if( file_exists($dir.'/'.$f)) {
					$path = pathinfo($f);

					$in_inc_files = (!empty($this->include_files) && in_array($f, $this->include_files));
					$no_inc_files = empty($this->include_files);

					if( $in_inc_files || $no_inc_files ) {
						$attr = array( 'value' => $f, 'data-file' => $path['filename']);
						$select .= tr_html::element( 'option', $attr, $this->clean_file_name($path['filename']) );
					}
				}
			}

			$select .= '</select>';

		} else {
			$select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a folder in Matrix called <code>{$this->name}</code> and add your matrix files to it.</div>";
		}

		return $select;

	}

	private function get_from() {

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
					$path = __DIR__ . "/" . $this->name . "/{$type}.php";

					if(file_exists($path)) {
						echo '<div class="matrix-field-group tr-repeater-group">';
						include($path);
						echo '</div></div>';
					} elseif(TR_DEBUG === true) {
						echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a file to your Matrix group <code>{$this->name}</code> and name it <code>{$type}.php</code>; ensure you require <code>tr_load_form.php</code>.</div>";
					}


					$form->group = $init_grp;
					$form->debug = true;
				}
			}

		}

	}

}