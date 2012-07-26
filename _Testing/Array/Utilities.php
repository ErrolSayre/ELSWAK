<?php
require_once 'ELSWAK/Array/Utilities.php';

$list = array(
	'one',
	'two',
	'three' => 'three',
	'four' => 'four',
	'five',
);

echo '<h1>Test Array</h1>', LF;
print_r_html($list);

$test = new ELSWAK_Array_Utilities;

echo '<h2>joinToEnglishListing</h2>', LF;
echo '<p>Testing successive lists by popping items off</p>', LF;
do {
	$count = count($list);
	echo '<p>';
	echo 'List of ', $count, ' item', ($count == 1? '': 's'), ' (defaults): ', BR,
		NBSPTAB, $test->joinToEnglishListing($list), BR, LF;

	echo 'List of ', $count, ' item', ($count == 1? '': 's'), ' (or): ', BR,
		NBSPTAB, $test->joinToEnglishListing($list, 'or'), BR, LF;

	echo 'List of ', $count, ' item', ($count == 1? '': 's'), ' (; no conjunction): ', BR,
		NBSPTAB, $test->joinToEnglishListing($list, null, true, '; '), BR, LF;

	echo 'List of ', $count, ' item', ($count == 1? '': 's'), ' (no Oxford Comma or): ', BR,
		NBSPTAB, $test->joinToEnglishListing($list, 'or', false), BR, LF;
	echo '</p>', LF;

	array_pop($list);
} while (count($list));