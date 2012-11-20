<?php
// locate the path to the "include path"
$path = dirname(dirname(__FILE__));

// setup helpful constants
require $path.'/StandardConstants.php';

// setup the auto-loader
// step the path up one more hop to allowe referencing with the class prefix
$path = dirname($path);
require_once $path.'/ELSWAK/ClassLoader.php';
$cal = new ELSWAK_ClassLoader(false, $path);