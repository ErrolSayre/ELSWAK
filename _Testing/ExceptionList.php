<?php
try {
	$list = new ELSWAK_ExceptionList;
} catch (Exception $e) { echo $e->getMessage(), BR, LF; }
try {
	$list = new ELSWAK_ExceptionList(array(new Exception('Your mom'), 'string'));
} catch (Exception $e) { echo 'Exception thrown: ', $e->getMessage(), BR, LF; }
