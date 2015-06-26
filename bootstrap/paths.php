<?php
return array(
	'assets'  => __DIR__ . '/../assets',
	'plugins' => __DIR__ . '/../plugins',
	'urls'    => array(
		'theme'   => get_stylesheet_directory_uri(),
		'assets'  => get_stylesheet_directory_uri() . '/' . self::$folder . '/assets',
		'plugins' => get_stylesheet_directory_uri() . '/' . self::$folder . '/plugins'
	)
);