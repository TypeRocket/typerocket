<?php
/*
 * PHP Server Router
 *
 * To emulate friendly URLs in the browser run from the wordpress folder:
 * |> php -S localhost:8888 ../server.php
 */
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
if ($uri !== '/' && file_exists(__DIR__.'/wordpress'.$uri)) {
    return false;
}
require_once __DIR__.'/wordpress/index.php';