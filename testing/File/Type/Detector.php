<?php
require 'ELSWebAppKit/File/Type/Detector.php';

// construct a new detector
$td = new ELSWebAppKit_File_Type_Detector();

$files = array
(
	'george.xpf',
	'main.cpp',
	'howdee.qtf',
	'12.rar',
);

foreach ($files as $file)
	echo $file.' ('.$td->typeFromName($file).')'.BR.LF;

echo __FILE__.' ('.ELSWebAppKit_File_Type_Detector::typeFromFile(__FILE__).')'.BR.LF;