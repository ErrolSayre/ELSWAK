<?php
// setup handy defaults
if (!defined('LF')) { define('LF', "\n"); }

// locate the path to the "include path"
$path = pathinfo(pathinfo(pathinfo(__FILE__, PATHINFO_DIRNAME), PATHINFO_DIRNAME), PATHINFO_DIRNAME);

// setup the auto-loader
require_once $path.'/ELSWAK/ClassLoader.php';
$cal = new ELSWAK_ClassLoader(false, $path);