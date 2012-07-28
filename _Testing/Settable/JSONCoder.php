<?php
require_once '../setup-environment.php';

class TestClass extends ELSWAK_Settable {
	protected $name;
	protected $label;
	protected $id;
}
$coder = new ELSWAK_Settable_JSONCoder('TestClass');

$object = new TestClass(array('name' => 'Errol', 'id' => 1232));
echo 'Original Object', LF;
print_r($object);
echo LF;

$data = $coder->encode($object);
echo 'Encoded JSON', LF;
echo $data, LF;
echo LF;

$decoded = $coder->decode($data);
echo 'Decoded Object', LF;
print_r($decoded);
echo LF;