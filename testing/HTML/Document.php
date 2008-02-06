<?php
require_once('ELSWebAppKit/HTML/Document.php');
// create a new document
$document = new ELSWebAppKit_HTML_Document();

// set the title
$document->setPageTitle('Element search test with large document');

// add an element with an id
$h1 = $document->body()->appendChild($document->createElement('h1'));
$h1->appendChild($document->createTextNode('Adding "firstHeader" node'));
$h1->setAttribute('id','firstHeader');

// add another element with an id
$h1 = $document->body()->appendChild($document->createElement('h1'));
$h1->appendChild($document->createTextNode('Adding "secondHeader" node'));
$h1->setAttribute('id','secondHeader');

// add another element with an id
$h1 = $document->body()->appendChild($document->createElement('h1'));
$h1->appendChild($document->createTextNode('Adding "thirdHeader" node'));
$h1->setAttribute('id','thirdHeader');

// add another element with an id and register it
$h1 = $document->body()->appendChild($document->createElement('h1'));
$h1->appendChild($document->createTextNode('Adding "fourthHeader" node'));
$h1->setAttribute('id','fouthHeader');
$document->registerElementWithIdIndex($h1);

// search for the first header
$start = microtime(true);
$firstHeader = $document->locateElementById('firstHeader');
$end = microtime(true);
$document->body()->appendChild($document->createElement('h2'))->appendChild($document->createTextNode('Found "firstHeader" node in '.number_format($end - $start, 10).' seconds'));
if ($firstHeader !== null)
{
	// style the text red
	$firstHeader->setAttribute('style','color:#E00000;');
}

// search for the third header (it should not be cached)
$start = microtime(true);
$thirdHeader = $document->locateElementById('thirdHeader');
$end = microtime(true);
$document->body()->appendChild($document->createElement('h2'))->appendChild($document->createTextNode('Found "thirdHeader" node in '.number_format($end - $start, 10).' seconds'));
if ($thirdHeader !== null)
{
	// style the text red
	$thirdHeader->setAttribute('style','color:#0000E0;');
}

// search for the fourth header (it should not be cached)
$start = microtime(true);
$thirdHeader = $document->locateElementById('thirdHeader');
$end = microtime(true);
$document->body()->appendChild($document->createElement('h2'))->appendChild($document->createTextNode('Found "thirdHeader" node in '.number_format($end - $start, 10).' seconds'));
if ($thirdHeader !== null)
{
	// style the text red
	$thirdHeader->setAttribute('style','color:#0000E0;');
}

// search for the second header (it should now be cached)
$start = microtime(true);
$secondHeader = $document->locateElementById('secondHeader');
$end = microtime(true);
$document->body()->appendChild($document->createElement('h2'))->appendChild($document->createTextNode('Found "secondHeader" node in '.number_format($end - $start, 10).' seconds'));
if ($secondHeader !== null)
{
	// style the text red
	$secondHeader->setAttribute('style','color:#00E000;');
}

// remove everything up to the first header we added
if (isset($_REQUEST['big']))
{
	$document->body()->appendChild($document->createElement('h2'))->appendChild($document->createTextNode('Removing prior content from large file (4MB @ 15,000 lines)'));
	$currentNode = $firstHeader->previousSibling;
	while ($currentNode != null)
	{
		$previous = $currentNode->previousSibling;
		$currentNode->parentNode->removeChild($currentNode);
		$currentNode = $previous;
	}
}

// create some other elements using the built-in functions

// output the document content
echo $document->saveXML();
?>