<?php define('WP_END', microtime(true));

function tr_dev_plugins() {

  $plugins = tr::$plugins;

  echo '<h3><i class="tr-icon-cog"></i> Active Plugins</h3><p>Active TypeRocket plugins being used on this site.</p><ul>';
  foreach($plugins as $k => $v) {
    echo tr_html::element('li', array(), $v);
  }
  echo '</ul>';

}

function tr_dev_icons() {

  $plugins = tr_icons::$icon;

  echo '<h3><i class="tr-icon-tools"></i> Icons</h3><p>These can be used with custom post types.</p><ol>';
  foreach($plugins as $k => $v) {
    echo tr_html::element('li', array('class' => 'tr-icon-'.$k), ' ' . $k . ' ( .tr-icon-' . $k . ' )');
  }
  echo '</ol>';

}

function tr_dev_forms() {

  $fields = array('post_title', 'post_content', 'post_type', 'post_status', 'post_author', 'post_excerpt', 'comment_status');

  echo '<h3><i class="tr-icon-pencil"></i> Form Building</h3><p>These are some of the field names for posts in the WordPress database. Helping when you need to make front-end forms to <a href="http://codex.wordpress.org/Function_Reference/wp_insert_post" target="_blank">create</a> or <a href="http://codex.wordpress.org/Function_Reference/wp_update_post" target="_blank">update</a> posts. TypeRocket will add underscores and lowercase field names for you. When updating a user try the field names at <a href="http://codex.wordpress.org/Function_Reference/wp_update_user" target="_blank">http://codex.wordpress.org/Function_Reference/wp_update_user</a></p><ul>';
  foreach($fields as $v) {
    echo tr_html::element('li', array(), $v);
  }

  echo '</ul>';

}

function tr_dev_stats() {

  if(defined('WP_START')) : ?>

    <h3><i class="tr-icon-pie"></i> Run Time and Memory Stats</h3>
    <p>If you are using xDebug profiling or tracking times will be slower.</p>

    <b>TR Run Time</b>: <?php echo TR_END - TR_START; ?><br>
    <b>Memory Use</b>: <?php echo memory_get_usage() / 1024 / 1024; ?> MB<br>
    <b>Peak Memory Use</b>: <?php echo memory_get_peak_usage() / 1024 / 1024; ?> MB

  <?php else : ?>

    <p>Add this code in your wp-config.php file. This will enable debug stats.</p>
    <p><code>define('WP_START', microtime(true));</code></p>

  <?php endif;
} ?>

  <h4>Updating Post Example (if on the single page)</h4>
  <code><pre>$form = new tr_form();
$form->make('post', 'update', $post->ID);
$form->process();
$form->open();
$form->text('Post Title', array(), array('builtin' => true));
$form->textarea('Post Content', array(), array('builtin' => true));
$form->close('Update');</pre></code>
  <h4>Creating Post Example</h4>
  <code><pre>$form = new tr_form();
$form->make('post', 'create');
$form->create_statics = array(
  'post_status' => 'publish',
  'post_type' => 'post'
);
$form->process();
$form->open();
$form->text('Post Title', array(), array('builtin' => true));
$form->textarea('Post Content', array(), array('builtin' => true));
$form->close('Create');</pre></code>
  <h4>Updating Current User Example</h4>
  <code><pre>$form = new tr_form();
$form->make('user', 'update', get_current_user_id());
$form->process();
$form->open();
$form->text('Last Name', array(), array('builtin' => true ));
$form->text('First Name', array(), array('builtin' => true ));
$form->close('Update Your Last Name');</pre></code> <?php

}
?>
<div class="wrap">
<h2>TypeRocket Dev</h2>


  <div>
    <div id="tr-dev-content" class="typerocket-container typerocket-dev">
    <?php
    $screen = new tr_layout();
    $screen->add_tab( array(
      'id' => 'stats',
      'title' => 'Stats',
      'content' => '',
      'callback' => 'tr_dev_stats'
    ) )->add_tab( array(
      'id' => 'plugins',
      'title' => 'Plugins',
      'content' => '',
      'callback' => 'tr_dev_plugins'
    ) )->add_tab( array(
        'id' => 'icons',
        'title' => 'Icon Names',
        'content' => '',
        'callback' => 'tr_dev_icons'
      ))->add_tab( array(
        'id' => 'forms',
        'title' => 'Form Building',
        'content' => '',
        'callback' => 'tr_dev_forms'
      ) )->make();  ?>
    </div>
  </div>

</div>

<?php

function tr_dev_stats() {

if(defined('WP_START')) :

?>

<h3><i class="tr-icon-pie"></i> Run Time and Memory Stats</h3>
<p>If you are using xDebug profiling or tracking times will be slower.</p>

<b>TR Run Time</b>: <?php echo TR_END - TR_START; ?><br>
<b>Memory Use</b>: <?php echo memory_get_usage() / 1024 / 1024; ?> MB<br>
<b>Peak Memory Use</b>: <?php echo memory_get_peak_usage() / 1024 / 1024; ?> MB

<?php else : ?>

<p>Add this code in your wp-config.php file. This will enable debug stats.</p>
<p><code>define('WP_START', microtime(true));</code></p>

<?php

endif;

}

function tr_dev_plugins() {

  $plugins = tr::$plugins;

  echo '<h3><i class="tr-icon-cog"></i> Active Plugins</h3><p>Active TypeRocket plugins being used on this site.</p><ul>';
  foreach($plugins as $k => $v) {
    echo tr_html::element('li', array(), $v);
  }
  echo '</ul>';

}

function tr_dev_icons() {

  $plugins = tr_icons::$icon;

  echo '<h3><i class="tr-icon-tools"></i> Icons</h3><p>These can be used with custom post types.</p><ol>';
  foreach($plugins as $k => $v) {
    echo tr_html::element('li', array('class' => 'tr-icon-'.$k), ' ' . $k . ' ( .tr-icon-' . $k . ' )');
  }
  echo '</ol>';

}

function tr_dev_forms() {

  $fields = array('post_title', 'post_content', 'post_type', 'post_status', 'post_author', 'post_excerpt', 'comment_status');

  echo '<h3><i class="tr-icon-pencil"></i> Form Building</h3><p>These are some of the field names for posts in the WordPress database. Helping when you need to make front-end forms to <a href="http://codex.wordpress.org/Function_Reference/wp_insert_post" target="_blank">create</a> or <a href="http://codex.wordpress.org/Function_Reference/wp_update_post" target="_blank">update</a> posts. TypeRocket will add underscores and lowercase field names for you. When updating a user try the field names at <a href="http://codex.wordpress.org/Function_Reference/wp_update_user" target="_blank">http://codex.wordpress.org/Function_Reference/wp_update_user</a></p><ul>';
  foreach($fields as $v) {
    echo tr_html::element('li', array(), $v);
  }
  ?></ul>
<h4>Updating Post Example (if on the single page)</h4>
<code><pre>$form = new tr_form();
$form->make('post', 'update', $post->ID);
$form->process();
$form->open();
$form->text('Post Title', array(), array('builtin' => true));
$form->textarea('Post Content', array(), array('builtin' => true));
$form->close('Update');</pre></code>
<h4>Creating Post Example</h4>
<code><pre>$form = new tr_form();
$form->make('post', 'create');
$form->create_statics = array(
  'post_status' => 'publish',
  'post_type' => 'post'
);
$form->process();
$form->open();
$form->text('Post Title', array(), array('builtin' => true));
$form->textarea('Post Content', array(), array('builtin' => true));
$form->close('Create');</pre></code>
<h4>Updating Current User Example</h4>
  <code><pre>$form = new tr_form();
$form->make('user', 'update', get_current_user_id());
$form->process();
$form->open();
$form->text('Last Name', array(), array('builtin' => true ));
$form->text('First Name', array(), array('builtin' => true ));
$form->close('Update Your Last Name');</pre></code> <?php

}