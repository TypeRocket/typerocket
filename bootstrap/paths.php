<?php
return array(
  'core' => __DIR__ . '/../core',
  'assets' => __DIR__ . '/../core/assets',
  'plugins' => __DIR__ . '/../plugins',
  'base' => __DIR__ . '/..',
  'urls' => array(
    'theme' => get_stylesheet_directory_uri(),
    'core' => get_stylesheet_directory_uri() . '/'.TR_FOLDER.'/core/',
    'assets' => get_stylesheet_directory_uri() . '/'.TR_FOLDER.'/core/assets',
    'plugins' => get_stylesheet_directory_uri() . '/'.TR_FOLDER.'/plugins'
  )
);