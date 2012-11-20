<?php
require_once 'setup-environment.php';

$runTimes = array();
class ELSWAK_User_Test extends ELSWAK_User {
	protected static $keyFactor;
	public static function setKeyFactor($value) {
		self::$keyFactor = $value;
	}
	public static function keyFactor() {
		if (self::$keyFactor) {
			return self::$keyFactor;
		}
		return parent::keyFactor();
	}
}

// copy the arguments list and pop off the script name0
$password = 'test123';
$passwords = array();
$arguments = $argv;
array_shift($arguments);

// process each argument looking for a password or key factor
foreach ($arguments as $arg) {
	if (is_numeric($arg)) {
		ELSWAK_User_Test::setKeyFactor($arg);
	} elseif ($password == 'test123') {
		$password = $arg;
	} else {
		$passwords[] = $arg;
	}
}

// make sure there are some passwords to test
if (!count($passwords)) {
	$passwords = array(
		'test1',
		'',
		'asdflkjj@~@!!kfhn2lkjhn	408s9caudjf;lkj2 34p 98/321[490	1823',
	);
}


$user = new ELSWAK_User_Test;
print_r($user);

echo 'Testing password hash using key factor of ', $user->keyFactor, LF;

$start = microtime(true);
echo '  Verify empty password "" against blank hash: ', ($user->verifyPassword('')? 'valid': 'invalid'), LF;
echo '  Verification ran in ', round(microtime(true) - $start, 4), 's', LF;

echo LF;

echo '  Setting password to "', $password, '"', LF;
$start = microtime(true);
$user->setPassword($password);
$end = microtime(true);
echo '  ', $user->passwordHash, LF;
echo '  Password set in ', round($end - $start, 4), 's', LF;

$start = microtime(true);
echo '  Verify password "', $password, '": ', ($user->verifyPassword($password)? 'valid': 'invalid'), LF;
$runTimes[] = microtime(true) - $start;
echo '  Verification ran in ', round($runTimes[count($runTimes) - 1], 4), 's', LF;



// test several other passwords
foreach ($passwords as $password) {
	echo LF;
	
	$start = microtime(true);
	echo '  Verify password "', $password, '": ', ($user->verifyPassword($password)? 'valid': 'invalid'), LF;
	$runTimes[] = microtime(true) - $start;
	echo '  Verification ran in ', round($runTimes[count($runTimes) - 1], 4), 's', LF;
	
	echo '  Setting password to "', $password, '"', LF;
	$start = microtime(true);
	$user->setPassword($password);
	$end = microtime(true);
	echo '  ', $user->passwordHash, LF;
	echo '  Password set in ', round($end - $start, 4), 's', LF;
	
	$start = microtime(true);
	echo '  Verify password "', $password, '": ', ($user->verifyPassword($password)? 'valid': 'invalid'), LF;
	$runTimes[] = microtime(true) - $start;
	echo '  Verification ran in ', round($runTimes[count($runTimes) - 1], 4), 's', LF;
}

echo LF;
echo 'Password verification average time: ', round(array_sum($runTimes) / count($runTimes), 4), 's', LF, LF;
