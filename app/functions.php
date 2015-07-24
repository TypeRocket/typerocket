<?php
function tr_taxonomy(
    $singular,
    $plural = null,
    $settings = array()
) {
    $obj = new \TypeRocket\Taxonomy($singular, $plural, $settings);
    $obj->addToRegistry();

    return $obj;
}

function tr_post_type(
    $singular,
    $plural = null,
    $settings = array()
) {
    $obj = new \TypeRocket\PostType($singular, $plural, $settings);
    $obj->addToRegistry();

    return $obj;
}

function tr_metabox(
    $name = null,
    $settings = array()
) {
    $obj = new \TypeRocket\Metabox($name, $settings);
    $obj->addToRegistry();

    return $obj;
}

function tr_tabs()
{
    return new \TypeRocket\Tabs();
}

function tr_buffer()
{
    return new \TypeRocket\Buffer();
}

/**
 * Instance the From
 *
 * @param string $controller posts, users, comments or options
 * @param string $action update or create
 * @param null|int $item_id you can set this to null or an integer
 *
 * @return \TypeRocket\Form
 */
function tr_form($controller = 'auto', $action = 'update', $item_id = null )
{
    return new \TypeRocket\Form($controller, $action, $item_id);
}

function tr_posts_field( $name, $item_id = null )
{
    global $post;

    if (isset( $post->ID ) && is_null( $item_id )) {
        $item_id = $post->ID;
    }

    $getter = new \TypeRocket\GetValue();

    return $getter->getFromBrackets( $name, $item_id, 'posts' );
}

function tr_users_field( $name, $item_id = null )
{
    global $user_id, $post;

    if (isset( $user_id ) && is_null( $item_id )) {
        $item_id = $user_id;
    } elseif (is_null( $item_id ) && isset( $post->ID )) {
        $item_id = get_the_author_meta( 'ID' );
    } elseif (is_null( $item_id )) {
        $item_id = get_current_user_id();
    }

    $getter = new \TypeRocket\GetValue();

    return $getter->getFromBrackets( $name, $item_id, 'users' );
}

function tr_options_field( $name )
{
    $getter = new \TypeRocket\GetValue();

    return $getter->getFromBrackets( $name, null, 'options' );
}

function tr_comments_field( $name, $item_id = null )
{
    global $comment;

    if (isset( $comment->comment_ID ) && is_null( $item_id )) {
        $item_id = $comment->comment_ID;
    }

    $getter = new \TypeRocket\GetValue();

    return $getter->getFromBrackets( $name, $item_id, 'comments' );
}