<?php
$handler = new ELSWAK_HTML_Handler;
$response = new ELSWAK_HTML_Response;
$form = $response->createForm();
$response->addContent($form);

// define some test questions
class testQuestionnaire
	extends ELSWAK_ManagedObject_Questionnaire_Abstract {
	public static function defineQuestions() {
		$questions = array();
		// !string questions
		$questions['HOW-DO-I-KNOW'] = array();
		$questions['LABEL-AS-TEXT'] = 'The label: ';
		$questions['LABEL-IN-KEY'] = array(
			'label' => 'This label is in an array',
		);
		
		// !text questions
		$questions['PROJECT-TITLE'] = array(
			'label' => 'Project Title',
			'property' => 'title',
			'type' => 'text',
			'class' => 'wordstatus words-150',
			'description' => 'Enter project title as shown on proposal cover sheet or title page.',
		);
		$questions['FISCAL-YEAR'] = array(
			'type' => 'year',
			'class' => 'currency',
			'label' => 'Specify the Federal Fiscal Year you are seeking funding for.',
		);
		$questions['SPONSOR-ACTIVITIES'] = array(
			'type' => 'text',
			'label' => 'If the project is currently sponsored, describe the sponsor’s main activities and regional significance.',
			'description' => 'Please specifically mention the sponsor type as public, private, non-profit, etc.',
		);
		
		// !select questions
		$questions['SELECT-MENU'] = array(
			'type' => 'select',
			'description' => 'Please select an option that is quick.',
			'values' => array(
				'FOX' => 'The Fox',
				'FROG' => 'Mr Bullfrog',
				'DOG' => 'Kep',
				'TURTLE' => 'Box, Box Turtle',
			),
		);
		
		// !checkbox questions
		$questions['SINGLE-CHECKBOX'] = array(
			'type' => 'checkbox',
			'label' => 'Do you agree?',
			'values' => array(
				'YES' => 'I have read this statement',
			),
		);
		$questions['MULTIPLE-CHECKBOX'] = array(
			'type' => 'checkbox',
			'label' => 'Which colors does your tv show you?',
			'field class' => 'highlight green',
			'values' => array(
				'RED' => 'Red',
				'GREEN' => 'Green',
				'BLUE' => 'Blue',
				'YELLOW' => 'Yellow',
			),
		);
		
		// !radio questions
		$questions['PROJECT-JUSTIFICATION'] = array(
			'type' => 'radio',
			'label' => 'Which one of these things is most yellow?',
			'field class' => 'highlight',
			'values' => array(
				'BUS' => 'School bus',
				'LEMON' => 'Lemon',
				'BANANA' => 'Banana',
				'LOOKAROUNDYOU' => 'Sulfur',
				'CREAM' => 'Cream',
				'BUTTER' => 'Butter',
			),
			'description' => 'Your decision here is of the utmost importance.',
		);
		
		// !yes/no questions
		$questions['AUTHORIZED-PROGRAM'] = array(
			'type' => 'yes/no',
			'label' => 'Are you authorized?',
			'description' => 'If Yes, the next field is for you.'
		);
		
		// !item questions
		$questions['CONTACT'] = array(
			'type' => 'item',
			'label' => false,
			'field class' => 'float',
			'item' => array(
				'title' => 'Title',
				'suffix' => 'Suffix',
				'phone' => array(
					'label' => 'Telephone',
					'class' => 'phone',
				),
				'email' => 'Email',
			),
		);
		$questions['CONSULTANT'] = array(
			'type' => 'item',
			'field class' => 'float',
			'label' => 'Consultant',
			'item' => array(
				'name' => 'Name',
				'title' => 'Title',
				'agency' => array(),
				'address' => array(
					'type' => 'item',
					'label' => false,
					'field class' => 'float',
					'item' => array(
						'line1' => 'Address',
						'line2' => 'Line 2',
						'city' => 'City',
						'state' => array(
							'type' => 'select',
							'values' => ELSWAK_Postal_Address::states(),
						),
						'zip' => array(
							'label' => 'ZIP',
							'size' => 11,
						),
					),
				),
				'phone' => array(
					'label' => 'Telephone',
					'class' => 'phone',
					'field class' => 'float',
				),
				'fax' => array(
					'label' => 'FAX',
					'class' => 'phone',
				),
				'email' => 'Email',
			)
		);
		
		// !list questions
		$questions['PRIOR-FUNDING'] = array(
			'type' => 'list',
			'label' => 'Additional Prior Funding',
			'item' => array(
				'summary' => array(
					'type' => 'item',
					'label' => false,
					'field class' => 'float',
					'item' => array(
						'source' => array(
							'label' => 'Funding Source',
							'class' => 'unit autocomplete',
						),
						'amount' => array(
							'label' => 'Award Amount',
							'class' => 'currency'
						),
						'dateReceived' => array(
							'label' => 'Date Received',
							'class' => 'date',
						),
					),
				),
				'expenditures' => array(
					'type' => 'text',
					'label' => 'How were funds specifically spent?'
				),
				'outcomes' => array(
					'type' => 'text',
					'label' => 'List tangible project outcomes'
				),
			),
		);
		
		// !html display
		$questions['HTML-DISPLAY'] = array(
			'type' => 'html',
			'content' => '<p class="warning">This is a warning</p><p>And this is not</p>',
		);
		return $questions;
	}
	public static function defineViews() {
		return array();
	}
}
$questionnaire = new testQuestionnaire;

// setup some test values
$values = array(
	'PRIOR-FUNDING' => array(
		array(
			'summary' => array(
				'source' => 'Source 1',
				'amount' => 392092.00,
				'dateReceived' => time() - TIME_ONE_DAY * 39,
			),
			'expenditures' => 'asdf qwer zxcv',
			'outcomes' => 'zxcv qwer asdf',
		),
		array(
			'summary' => array(
				'source' => 'Source 2',
				'amount' => 585924.25,
				'dateReceived' => time() - TIME_ONE_DAY * 390,
			),
			'expenditures' => 'asdf qwer zxcv',
			'outcomes' => 'zxcv qwer asdf',
		),
	),
);

// process the questions
$questions = array_keys($questionnaire->questions());
foreach ($questions as $question) {
	try {
		if (array_key_exists($question, $values)) {
			$form->appendChild($handler->formFieldForQuestionCode($response, $questionnaire, $question, null, $values[$question]));
		} else {
			$form->appendChild($handler->formFieldForQuestionCode($response, $questionnaire, $question));
		}
	} catch (Exception $e) { $response->addMessage($e->getMessage()); }
}
$response->send();