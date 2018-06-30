<?php

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

$icons = function()
{
    $icons = \TypeRocket\Core\Config::locate('app.class.icons');
    $icons = new $icons;
    $generator = new \TypeRocket\Html\Generator();

    echo '<h3><i class="tr-icon-tools"></i>' . __('Icons') . '</h3>';
    echo '<p>' . __('These can be used with custom post types and admin pages.');
    echo '</p><p><input onkeyup="trDevIconSearch()" placeholder="' . __('Enter text to search list...') . '" id="dev-icon-search" /></p><ol id="debug-icon-list">';
    foreach ($icons as $k => $v) {
        echo $generator->newElement( 'li', ['class' => 'tr-icon-' . $k, 'id' => $k],
            '<strong>' . $k . '</strong><em>.tr-icon-' . $k . '</em>' )->getString();
    }
    echo '</ol>';
    ?>
    <script language="JavaScript">
        function trDevIconSearch() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("dev-icon-search");
            filter = input.value.toUpperCase();
            ul = document.getElementById("debug-icon-list");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i];
                if (a.id.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
    </script>
    <?php
};

$rules = function() {
    echo '<h3><i class="tr-icon-tools"></i>' . __('Rewrite Rules') . '</h3>';
    $rules = get_option('rewrite_rules');
    if(!empty($rules)) {
        echo "<p>If you are using TypeRocket custom routes they will not appear in this list. TypeRocket detects custom routes on the fly.</p>";
        echo '<table class="wp-list-table widefat fixed striped">';
        echo "<thead><tr><th>" . __('Rewrite Rule') . "</th><th>" . __('Match') . "</th></tr></thead>";
        foreach ($rules as $rule => $match) {
            echo "<tr><td>$rule</td><td>$match</td></tr>";
        }
        echo '</table>';
    } else {
        echo "<p>Enable <a href=\"https://codex.wordpress.org/Using_Permalinks\">Pretty Permalinks</a> under <a href=\"/wp-admin/options-permalink.php\">Permalink Settings</a>. \"Pretty Permalinks\" are required for TypeRocket to work.</p>";
    }

};

$tabs = tr_tabs();
$tabs->addTab(__('Icons'), $icons)
    ->addTab(__('Rewrite Rules'), $rules)
    ->render('box');
?>
