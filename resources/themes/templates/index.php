<?php

/*
|--------------------------------------------------------------------------
| Main View
|--------------------------------------------------------------------------
|
| Here is where you can replace the standard WordPress templating system
| with TypeRocket views loaded from the configured paths.views folder.
|
*/

\TypeRocket\Template\View::new('master', ['title' => 'Antennae'])->render();