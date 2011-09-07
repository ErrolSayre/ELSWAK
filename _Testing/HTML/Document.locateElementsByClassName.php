<?php
/*
	This class specifically tests the ELSWAK_HTML_Document's abiliity to locate elements by class name.
*/
// load a new document with the provided template
$document = new ELSWAK_HTML_Document('./Document.locateElementsByClassName.html');

// process each test
$tests = array(
	'fancy',
	'wicked fancy',
	'wicked,fancy',
	null,
	false,
	'',
	'fancy wicked',
);
foreach ($tests as $test) {
	if ($test === false) {
		echo '<h1>Testing false</h1>', LF;
	} else if ($test === null) {
		echo '<h1>Testing null</h1>', LF;
	} else {
		echo '<h1>Testing "', $test, '"</h1>', LF;
	}
	$elements = $document->locateElementsByClassName($test);
	echo '<p>', count($elements), ' match', (count($elements) == 1? '': 'es'), '</p>', LF;
	foreach ($elements as $element) {
		printElement($element);
	}
}


function printElement(DOMElement $element) {
	echo '<p><b>', $element->nodeName, '</b>';
	if ($element->hasAttribute('class')) {
		echo ' (', $element->getAttribute('class'), ')';
	}
	if ($element->nodeValue) {
		echo ' ', $element->nodeValue;
	}
	echo '</p>', LF;
}