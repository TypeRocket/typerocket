<?php
$tr_load_ok = defined('TR_START');
if(!$tr_load_ok) :
	include( __DIR__ . '/config.php');
	require_once(WP_INIT_PATH . '/wp-load.php');
	require_once( get_template_directory() . '/typerocket/init.php' );

	$tr_matrix_id = time();
	$tr_matrix_group = $_GET['id'];
	$tr_matrix_type = $_GET['type'];


	$form = tr_form();
	$form->get_values = false;

	$form->group = "[{$tr_matrix_group}][{$tr_matrix_id}][{$tr_matrix_type}]";

else :
	$form = $this->form;

	$tr_matrix_id = $t;
	$tr_matrix_group = $this->name;
	$tr_matrix_type = $type;

	$form->group = "[{$tr_matrix_group}][{$tr_matrix_id}][{$tr_matrix_type}]";
endif;



