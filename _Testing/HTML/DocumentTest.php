<?php
class ELSWAK_HTML_DocumentTest
	extends PHPUnit\Framework\TestCase {

	public function testDocument() {
		// this test is adapted from the previous test
		$input = array();
		$input['phone'] = '';
		$input['pin'] = '';
		$input['TheRadio'] = '';
		$input['TheCheckBox1'] = '';
		$input['TheCheckBox2'] = '';
		$input['TheCheckBox3'] = '';
		$input['TheCheckBox4'] = '';
		$input['TheLabeledCheckBox1'] = '';
		$input['TheLabeledCheckBox2'] = '';
		$input['TheLabeledCheckBox3'] = '';
		$input['TheLabeledCheckBox4'] = '';
		$input['TheSelect1'] = '';
		$input['TheSelect2'] = '';
		$input['TheSelect3'] = '';
		$input['TheSelect4'] = '';
		
		// load the input if it's available
		foreach ($_REQUEST as $key => $value)
			$input[$key] = $value;
		
		// create a new document
		$document = new ELSWAK_HTML_Document;
		
		// set the title
		$this->assertEquals('Untitled HTML Document', $document->title());
		$document->setTitle('Document Test');
		$this->assertEquals('Document Test', $document->title());
		
		// add an element with an id
		$h1 = $document->body()->appendChild($document->createElement('h1'));
		$h1->appendChild($document->createTextNode('Adding "firstHeader" node'));
		$h1->setAttribute('id','firstHeader');
		
		// add another element with an id
		$h1 = $document->addContent($document->createElement('h1', 'Adding "secondHeader" node', array('id' => 'secondHeader')));
		
		// add another element with an id
		$document->addContent($document->createElement('h1', 'Adding "thirdHeader" node'), 'thirdHeader');
		
		// add another element with an id and register it
		$h1 = $document->body()->appendChild($document->createElement('h1'));
		$h1->appendChild($document->createTextNode('Adding "fourthHeader" node'));
		$h1->setAttribute('id','fouthHeader');
		$document->registerElementWithIdIndex($h1);
		
		// add another element by html
		$document->addContent('<h1>Adding "fifthHeader" node</h1>', 'fifthHeader', 'html');
		
		// add another element by string
		$document->addContent('<h1>Adding "sixthHeader" node</h1>', 'sixthHeader');
		
		// add another element by html
		$document->addContent('Adding "seventhHeader" node', 'seventhHeader', 'html');
		
		// create some other elements using the built-in functions
		$a = $document->addContent($document->createLink('http://www.apple.com', 'Amazing Computer Company', array('id' => 'TheAppleLink', 'title' => 'A REALLY COOL SITE')));
		$form = $document->addContent($document->createForm($_SERVER['PHP_SELF'], 'POST'));
		$fieldset = $form->appendChild($document->createFieldset('Some inputs'));
		$fieldset->appendChild($document->createFormField
		(
			'Phone Number',
			$document->createTextInput('phone', $input['phone'], array('id' => 'the phone input')),
			'Please enter your phone here'
		));
		$fieldset->appendChild($document->createFormField
		(
			'PIN Number',
			$document->createPasswordInput('pin', $input['pin'], array('id' => 'the pin input')),
			'Please enter your PIN here'
		));
		
		$div = $document->createElement('div', null, array('class' => 'input'));
		$div->appendChild($document->createTextInput('start', 'Feb 14, 2003'));
		$div->appendChild($document->createTextNode(' to '));
		$div->appendChild($document->createTextInput('start', 'Feb 24, 2003'));
		$fieldset->appendChild($document->createFormField('Period', $div));
		
		$fieldset = $form->appendChild($document->createFieldset('Some radios'));
		$fieldset->appendChild($document->createRadioInput('TheRadio', $value = 'A', ($input['TheRadio'] == 'A')? true: false));
		$fieldset->appendChild($document->createRadioInput('TheRadio', $value = 'B', ($input['TheRadio'] == 'B')? true: false));
		$fieldset->appendChild($document->createRadioInput('TheRadio', $value = 'C', ($input['TheRadio'] == 'C')? true: false));
		$fieldset->appendChild($document->createRadioInput('TheRadio', $value = 'D', ($input['TheRadio'] == 'D')? true: false));
		
		$fieldset = $form->appendChild($document->createFieldset('Some chcekboxes'));
		$fieldset->appendChild($document->createCheckboxInput('TheCheckBox1', $value = 'YES', ($input['TheCheckBox1'] == 'YES')? true: false));
		$fieldset->appendChild($document->createCheckboxInput('TheCheckBox2', $value = 'YES', ($input['TheCheckBox2'] == 'YES')? true: false));
		$fieldset->appendChild($document->createCheckboxInput('TheCheckBox3', $value = 'YES', ($input['TheCheckBox3'] == 'YES')? true: false));
		$fieldset->appendChild($document->createCheckboxInput('TheCheckBox4', $value = 'YES', ($input['TheCheckBox4'] == 'YES')? true: false));
		
		$fieldset = $form->appendChild($document->createFieldset('Some labeled checkboxes'));
		$fieldset->appendChild($document->createLabeledCheckboxInput('TheLabeledCheckBox1', $value = 'YES', 'Checkboxes are great', ($input['TheLabeledCheckBox1'] == 'YES')? true: false));
		$fieldset->appendChild($document->createLabeledCheckboxInput('TheLabeledCheckBox2', $value = 'YES', 'Checkboxes are awesome', ($input['TheLabeledCheckBox2'] == 'YES')? true: false));
		$fieldset->appendChild($document->createLabeledCheckboxInput('TheLabeledCheckBox3', $value = 'YES', 'Checkboxes are stupendous', ($input['TheLabeledCheckBox3'] == 'YES')? true: false));
		$fieldset->appendChild($document->createLabeledCheckboxInput('TheLabeledCheckBox4', $value = 'YES', 'Checkboxes are just ok', ($input['TheLabeledCheckBox4'] == 'YES')? true: false));
		
		$options = array();
		$options[] = array('value' => 'MS', 'content' => 'Mississippi');
		$options[] = array('value' => 'KY', 'content' => 'Kentucky');
		$options[] = array('value' => 'TN', 'content' => 'Tennessee');
		$options[] = array('value' => 'AL', 'content' => 'Alabama');
		
		$fieldset = $form->appendChild($document->createFieldset('Some selects'));
		if (!empty($input['TheSelect1']))
			$fieldset->appendChild($document->createSelect('TheSelect1', array('value' => $input['TheSelect1'], 'content' => $input['TheSelect1']), $options, 'Please select a State'));
		else
			$fieldset->appendChild($document->createSelect('TheSelect1', null, $options, 'Please select a State'));
		$fieldset->appendChild($document->createSelect('TheSelect2', array('value' => $input['TheSelect2']), $options, 'Please select a State'));
		$fieldset->appendChild($document->createSelect('TheSelect3', $input['TheSelect3'], $options, 'Please select a different State'));
		$fieldset->appendChild($document->createSelect('TheSelect4', array('value' => $input['TheSelect4'], 'content' => ''), $options, 'Please select a State'));
		
		$fieldset = $form->appendChild($document->createFieldset());
		$fieldset->appendChild($document->createButtonInput('Button', 'The Button'));
		$fieldset->appendChild($document->createResetButtonInput('Reset', 'The Reset'));
		$fieldset->appendChild($document->createSubmitButtonInput('Submit', 'The Submit'));
		
		$document->addStylesheet('/DataGeneral/css/main.css');
		
		$document->addContent($document->debugDumpVariable($_POST, 'Post Data'));
		
		$document->addContent($document->migrateElementToElementOfType($document->createElement('h1', 'Migrate this H1 to an H2', array('style' => 'color: red;')), 'h2'));
		
		$document->addContent($document->migrateElementToElementOfType($document->convertHTML('<p style="color: blue;">Convert and migrate this paragraph html <i>with sub element</i> to an H1</p>'), 'h1'));
		
		
		// write the document to a file
		$path = pathinfo(__FILE__, PATHINFO_DIRNAME).'/'.pathinfo(__FILE__, PATHINFO_FILENAME).'.';
		$document->saveHTMLFile($path.'actual.html');
		
		// compare the written document to the expected
		$this->assertFileEquals($path.'expected.html', $path.'actual.html');
	}
}