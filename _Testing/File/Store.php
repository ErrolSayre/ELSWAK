<?php
require_once '../setup-environment.php';

class TestClass extends ELSWAK_Settable {
	protected $name;
	protected $label;
	protected $id;
}

// create a temp file to store the data in
$file = trim(shell_exec('mktemp -t ELSWAK_File_Store_testfile'));
$store = new ELSWAK_File_Store($file);

// create an object
echo 'Original Object', LF;
$object = new TestClass(array('name' => 'Errol', 'id' => 3950));
print_r($object);
echo LF;

// store the file
echo 'Storing Object at: ', $file, LF;
$store->write($object);
echo file_get_contents($file), LF;
echo LF;

// read the data back in
echo 'Read Object', LF;
try {
	$read = $store->read();
	print_r($read);
} catch (Exception $e) {
	echo $e->getMessage(), LF;
}
echo LF;

// delete the temp file
unlink($file);
