<?php
$coder = new ELSWAK_VariableAlphabetNumericCoder;

if (isset($argc)) {
	// process the first 100 items from each significant binary step (powers of two)
	$power = 0;
	while ($power < 64) {
		$current = (int) pow(2, $power);
		$start = $current;
		$first = 0;
		echo 'Starting at ', $start, '(2^', $power, ')', LF;
		$checks = 0;
		$matches = 0;
		try {
			while ($matches < 5 && $checks < 100) {
				$encoded = $coder->encode($current);
				$decoded = $coder->decode($encoded);
				if ($decoded != $current) {
					echo 'Unencodable integer: ', $current, LF;
					$errors[] = $current;
					++$matches;
					if ($first == null) {
						$first = $current;
					}
				}
				++$current;
				++$checks;
			}
		} catch (Exception $e) {
			echo $e->getMessage(), LF;
		}
		if ($first != null) {
			echo 'First unencodable integer: ', $first, LF;
			echo 'Difference from start: ', $first - $start, LF;
		}
		++$power;
	}
	//print_r($errors);
} else {
	echo '<h1>Test values</h1>', LF;
	$numbers = array(rand());
	if (isset($_REQUEST['number'])) {
		$numbers[] = intval($_REQUEST['number']);
	}
	$numbers[] = getrandmax();
	$numbers[] = 9223372036854775799;
	$numbers[] = 9223372036854775800;
	$numbers[] = 9223372036854775801;
	$numbers[] = 9223372036854775807;
	$numbers[] = PHP_INT_MAX;
	
	foreach ($numbers as $number) {
		echo '<h2>Encoding ', $number, '</h2>', LF;
		try {
			$encoded = $coder->encode($number);
			echo $encoded, BR, LF;
		} catch (Exception $e) { echo $e->getMessage(), BR, LF; }
		echo '<h2>Decoding ', $encoded, '</h2>', LF;
		try {
			$decoded = $coder->decode($encoded);
			echo $decoded, BR, LF;
			echo $number, ' - ', $number == $decoded? 'Equal': 'Not Equal';
		} catch (Exception $e) { echo $e->getMessage(), BR, LF; }
	}
}