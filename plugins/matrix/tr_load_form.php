<?php
// test to see if this is an AJAX call
$tr_load_ok = defined('TR_START');
if(!$tr_load_ok) :

	// test for config
	$config = __DIR__ . '/config.php';
	$debug = false;
	if(file_exists($config)) {
		require($config);
		require_once(WP_INIT_PATH . '/wp-load.php');
		require_once( get_template_directory() . '/'.TR_INIT_FOLDER.'/init.php' );
	} else {
		$debug = true;
		echo '<div class="tr-dev-alert-helper"><i class="icon tr-icon-bug"></i>Copy the Matrix plugin <code>config.sample.php</code> file and rename it to <code>config.php</code>.</div>';
	}

	// test for user logged in
	if( function_exists('current_user_can') && current_user_can('read') ) {
		$tr_matrix_id = time();
		$tr_matrix_group = $_GET['id'];
		$tr_matrix_type = lcfirst($_GET['type']);
		$tr_matrix_form_group = $_GET['form_group'];

		$form = tr_form();
		$form->get_values = false;

		if(!$tr_matrix_form_group) {
			$tr_matrix_form_group = '';
		}

		$form->group = $tr_matrix_form_group . "[{$tr_matrix_group}][{$tr_matrix_id}][{$tr_matrix_type}]";
		echo '<div class="matrix-field-group tr-repeater-group matrix-type-'.$tr_matrix_type.' matrix-group-'.$tr_matrix_group.'">';
	} elseif($debug == false) {
		http_response_code(404);
		exit();
	} else {
		exit();
	}

endif; ?>

	<div class="repeater-controls">
		<div class="collapse"></div>
		<div class="move"></div>
		<a href="#remove" class="remove" title="remove"></a>
	</div>
	<div class="repeater-inputs">

