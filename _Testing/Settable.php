<?php
require_once 'setup-environment.php';
class example
	extends ELSWAK_Settable {
	protected $id;
	protected $title;
	protected $name;
	protected $email;
	protected $date;
	protected $gettable;
	protected $settable;
	protected $cerifications;
	protected $responses;
	
	public function __construct($import = null) {
		parent::__construct($import);
		$this->responses = array();
	}
	public function setId($value) {
		return $this->_setPropertyAsId('id', $value);
	}
	public function setName($name) {
		$this->name = strtoupper($name);
	}
	public function email() {
		// return the email address as a mailto link
		return str_replace('@', ' (at) ', $this->email);
	}
	public function emailLink() {
		return 'mailto:'.$this->email;
	}
	public function date($format = null) {
		if ($format !== null)
			return $this->_getPropertyAsDate('date', $format);
		return $this->date;
	}
	public function setDate($value) {
		return $this->_setPropertyAsTimestamp('date', $value);
	}
	protected function setGettable($value) {
		$this->gettable = $value;
		return $this;
	}
	protected function settable() {
		return $this;
	}
	public function virtual() {
		return $this->name.' ('.$this->email().')';
	}
	public function setVirtual($value) {
		$this->setName(substr($value, 0, strpos($value, ' (')));
		return $this;
	}
	public function setCertifications($list) {
		return $this->_setArrayProperty('certifications', $list);
	}
	public function _verifyCertificationsItem($item) {
		return is_string($item);
	}
	public function responseForQuestion($question) {
		if (isset($this->responses[$question])) {
			return $this->responses[$question];
		} else {
			$key = $this->_validateValueAgainstList($question, $this->questions());
			if (isset($this->responses[$key])) {
				return $this->responses[$key];
			}
		}
		return null;
	}
	public function setResponseForQuestion($response, $question) {
		try {
			$this->responses[
				$this->_keyForValueValidatedAgainstList($question, $this->questions())
			] = $response;
		} catch (Exception $e) {
			throw new Exception('Unable to set response for “'.$question.'”. Invalid question identifier.');
		}
		return $this;
	}
	public function removeResponseForQuestion($question) {
		return $this->_removeArrayPropertyItemForKey(
			'responses',
			$this->_keyForValueValidatedAgainstList($question, $this->questions())
		);
	}
	public static function questions() {
		return array(
			'MAIDEN' => 'Mother’s Maiden Name',
			'SCHOOL' => 'School where you attended first grade',
		);
	}
}

echo '<h1>Testing static methods</h1>', LF;
$values = array(
	'yes',
	'y',
	'true',
	'x',
	'Yes',
	'Ye',
	'Y',
	'no',
	'n',
	' ',
	'',
	'none',
	'not',
	'null',
	'none',
	'NO',
	'No',
	'n',
	'maybe',
	'sorta',
	'positively',
	'pending',
	'p',
	'nil',
	'NULL',
	'nil',
	'null' => null,
	'false' => false,
	'true' => true,
);

echo '<table style="border-collapse:collapse;">', LF;
echo '	<thead>', LF;
echo '		<tr>', LF;
echo '			<th>Value</th>', LF;
echo '			<th>valueAsBoolean</th>', LF;
echo '			<th>valueAsNullBoolean</th>', LF;
echo '		</tr>', LF;
echo '	</thead>', LF;
$trues = ELSWAK_Settable::acceptableTrueValues();
$falses = ELSWAK_Settable::acceptableFalseValues();
$nulls = ELSWAK_Settable::acceptableNullValues();

foreach ($values as $label => $value) {
	echo '		<tr style="border-top:1px solid black;">', LF;
	if (is_string($label)) {
		echo '			<td>', $label, '</td>', LF;
	} else {
		echo '			<td>“'.$value.'”</td>', LF;
	}
	
	$methods = array(
		'valueAsBoolean',
		'valueAsNullBoolean',
	);
	foreach ($methods as $method) {
		$bool = ELSWAK_Settable::$method($value);
		$style = '';
		if ($bool === true) {
			if (in_array(strtolower($value), $trues)) {
				$style = ' style="background-color:green;"';
			}
			echo '				<td', $style, '>true</td>', LF;
		} else if ($bool === false) {
			if (in_array(strtolower($value), $falses)) {
				$style = ' style="background-color:green;"';
			}
			echo '				<td', $style, '>false</td>', LF;
		} else if ($bool === null) {
			if (in_array(strtolower($value), $nulls)) {
				$style = ' style="background-color:green;"';
			}
			echo '				<td', $style, '>null</td>', LF;
		} else {
			echo '				<td>“'.$bool.'” - ', gettype($bool), '</td>', LF;
		}
	}
	
	echo '		</tr>', LF;
}
echo '	</tbody>', LF;
echo '</table>', LF;


echo '<h1>Creating var1</h1>'.LF;
$var1 = new example();

echo '<h2>Setting id to 230</h2>'.LF;
try { $var1->id = 230; echo $var1->id.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting title to "Dr."</h2>'.LF;
try { $var1->title = 'Dr.'; echo $var1->title.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting name to "John SMith"</h2>'.LF;
try { $var1->name = 'John SMith'; echo $var1->name.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting email to "joe@theplace.com"</h2>'.LF;
try { $var1->email = 'joe@theplace.com'; echo $var1->email.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting emailLink to "joe@theplace.com"</h2>'.LF;
try { $var1->emailLink = 'joe@theplace.com'; echo $var1->emailLink.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting emailLink() as property</h2>'.LF;
try { echo $var1->emailLink.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting date to "'.date('Y-m-d').'"</h2>'.LF;
try { $var1->date = date('Y-m-d'); echo $var1->date.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting title to "the thing" using title as setter method</h2>'.LF;
try { $var1->title('the thing'); echo $var1->title.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting date as property</h2>'.LF;
try { echo $var1->date.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting date() as datetime</h2>'.LF;
try { echo $var1->date('Y-m-d H:i:s').BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting virtual property</h2>'.LF;
try { echo $var1->virtual.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting Virtual as "Errol Sayre (esayre (at) olemiss.edu)"</h2>'.LF;
try { $var1->Virtual = 'Errol Sayre (esayre (at) olemiss.edu)'; echo $var1->virtual; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

$tests = array(
	array(
		'ACMT',
		'CDPT',
		'ELEE',
		'LCA',
		'LCE'
	),
	array(
		'ACPT',
		array(),
		'MARGC',
		new stdClass
	)
);
foreach ($tests as $input) {
	echo '<h2>Setting cerifications to "'.json_encode($input).'"</h2>'.LF;
	try { $var1->certifications = $input; echo json_encode($var1->certifications); } catch (Exception $e) { echo $e->getMessage().BR.LF; }
}

$tests = array(
	'maiden' => 'Lewis',
	'father' => 'Bertram',
	'Mother’s Maiden Name' => 'Louis',
	'MAIDEN' => 'Hooowah',
	'SCHOOL' => 'Arbuckle Elementary',
);
foreach ($tests as $question => $response) {
	echo '<h2>Setting response for “', $question, '” to “', $response, '”</h2>', LF;
	try { $var1->setResponseForQuestion($response, $question); echo $var1->responseForQuestion($question); } catch (Exception $e) { echo $e->getMessage(), BR, LF; }
}

echo '<h2>Resulting object</h2>'.LF;
var_dump($var1);

echo '<h2>Removing response for “Mother’s Maiden Name”</h2>', LF;
$var1->removeResponseForQuestion('MAIDEN');

echo '<h2>Exporting object to array</h2>'.LF;
var_dump($var1->_export);

echo '<h1>Creating var2 by injection of associative array into default constructor</h1>'.LF;
$var2 = new example(array('date' => time(), 'name' => 'George McDudal', 'settable' => 'Your mom', 'gettable' => 'Horray'));

echo '<h2>Resulting object</h2>'.LF;
var_dump($var2);

echo '<h2>Exporting object to array</h2>'.LF;
var_dump($var2->_export);

/*
echo '<h1>Creating var3 via factory method with associative array</h1>'.LF;
$var3 = example::_factory(array('date' => time(), 'name' => 'Another Person', 'settable' => 'Is it really settable?', 'gettable' => 'Doubt it’s gettable'));
*/