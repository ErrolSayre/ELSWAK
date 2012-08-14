<?php
require_once 'setup-environment.php';

$user = new ELSWAK_User;
//print_r($user);

echo 'Testing hash:', LF;
echo '  Setting password to "test"', LF;
$user->setPassword('test');
echo '  Verify password "test": ', ($user->verifyPassword('test')? 'valid': 'invalid'), LF;
echo '  Setting salt to "asdf"', LF;
$user->setSalt('asdf');
echo '  Verify password "test": ', ($user->verifyPassword('test')? 'valid': 'invalid'), LF;
