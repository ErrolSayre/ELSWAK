<?php
require_once 'setup-environment.php';

$runTimes = array();

$user = new ELSWAK_User;
//print_r($user);

echo 'Testing hash:', LF;
echo '  Setting password to "test"', LF;
$start = microtime(true);
$user->setPassword('test');
echo '  Password set in ', round(microtime(true) - $start, 4), 's', LF;
$start = microtime(true);
echo '  Verify password "test": ', ($user->verifyPassword('test')? 'valid': 'invalid'), LF;
$runTimes[] = microtime(true) - $start;
echo '  Setting salt to "asdf"', LF;
$user->setSalt('asdf');
$start = microtime(true);
echo '  Verify password "test": ', ($user->verifyPassword('test')? 'valid': 'invalid'), LF;
$runTimes[] = microtime(true) - $start;

echo 'Password verification average time: ', round(array_sum($runTimes) / count($runTimes), 4), 's', LF;