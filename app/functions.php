<?php
function tr_taxonomy(
	$singular,
	$plural,
	$settings = array()) {
	$obj = new \TypeRocket\Taxonomy();
	$obj->setup( $singular, $plural, $settings )->addToRegistry();

	return $obj;
}

function tr_post_type(
	$singular,
	$plural,
	$settings = array()
) {
	$obj = new \TypeRocket\PostType();
	$obj->setup( $singular, $plural, $settings )->addToRegistry();

	return $obj;
}

function tr_meta_box(
	$name = null,
	$settings = array()
) {
	$obj = new \TypeRocket\Metabox();
	$obj->setup( $name, $settings )->addToRegistry();

	return $obj;
}

function tr_form() {
	$obj = new \TypeRocket\Form();
	$obj->setup();
	return $obj;
}

function tr_post_field( $name, $item_id = null ) {
	global $post;

	if ( isset( $post->ID ) && is_null( $item_id ) ) {
		$item_id = $post->ID;
	}

	$getter = new \TypeRocket\GetValue();

	return $getter->getFromBrackets( $name, $item_id, 'posts' );
}

function tr_user_field( $name, $item_id = null ) {
	global $user_id, $post;

	if ( isset( $user_id ) && is_null( $item_id ) ) {
		$item_id = $user_id;
	} elseif ( is_null( $item_id ) && isset( $post->ID ) ) {
		$item_id = get_the_author_meta( 'ID' );
	} elseif ( is_null( $item_id ) ) {
		$item_id = get_current_user_id();
	}

	$getter = new \TypeRocket\GetValue();

	return $getter->getFromBrackets( $name, $item_id, 'users' );
}

function tr_option_field( $name ) {
	$getter = new \TypeRocket\GetValue();

	return $getter->getFromBrackets( $name, null, 'options' );
}

function tr_comment_field( $name, $item_id = null ) {
	global $comment;

	if ( isset( $comment->comment_ID ) && is_null( $item_id ) ) {
		$item_id = $comment->comment_ID;
	}

	$getter = new \TypeRocket\GetValue();

	return $getter->getFromBrackets( $name, $item_id, 'comments' );
}