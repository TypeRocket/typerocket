<?php
$tr_load_ok = defined('TR_START');
if(!$tr_load_ok) :

	include( __DIR__ . '/config.php');
	require_once(WP_INIT_PATH . '/wp-load.php');
	require_once( get_template_directory() . '/'.TR_INIT_FOLDER.'/init.php' );

	if( current_user_can('read') ) {
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
	} else {
		http_response_code(404);
		exit();
	}

endif; ?>

<div class="repeater-controls">
	<div class="move tr-icon-menu"></div>
	<a href="#remove" class="remove tr-icon-remove2" title="remove"></a>
</div>
<div class="repeater-inputs">


