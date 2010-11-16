<?php
/*
	ELSWAK HTML Handler
	
	This class provides a standard library of html display, and form read/write methods.
*/
class ELSWAK_HTML_Handler {
// =================== 
// !Display Methods   
// =================== 
	public static function navigationBar(ELSWAK_HTML_Response $response, ELSWAK_ManagedObject $record, array $viewTasks = null, array $editTasks = null, array $existenceTasks = null, array $availableTasks = null, $currentTask = null, array $attributes = null) {
		// create the container
		$attributes = $response->addClassToAttributesArray($record->modelClassLabel.' navigation bar', $attributes);
		$container = $response->createDiv($response->createElement('h1', $record->label), $attributes);
		
		// process the various tasks to create a listing
		$list = $container->appendChild($response->createUl(null, array('class' => $record->modelClassLabel.' task container')));
		
		// add each section as a subordinate list
		$sections = array('view', 'edit', 'existence');
		foreach ($sections as $section) {
			$sectionList = $response->createUl(null, array('class' => $record->modelClassLabel.' '.$section.' tasks'));
			foreach (${$section.'Tasks'} as $task => $taskData) {
				if (array_key_exists($task, $availableTasks) && $availableTasks[$task]) {
					$sectionList->appendChild($response->createLi($response->createLink($record->uri.'/'.$task, $taskData['label'], array('title' => $taskData['description'])), $currentTask == $task? array('class' => 'active'): null));
				}
			}
			if ($sectionList->hasChildNodes()) {
				$list->appendChild($response->createElement('li', $sectionList));
			}
		}
		return $container;
	}
	
	

// ==================================== 
// !Questionnaire ("Automagic Forms")   
// ==================================== 
	public static function questionnaireView(ELSWAK_HTML_Response $response, ELSWAK_ManagedObject_Questionnaire_Abstract $questionnaire, ELSWAK_ManagedObject $object, $view) {
		$form = $response->createForm($object->uri.'/'.$view);
		
		// add a header if necessary
		if ($view == 'create') {
			$form->appendChild($response->createH1('Create a new '.$object->modelClassLabel));
		}
		
		// add the sections for this view
		$sections = $questionnaire->sectionsForView($view);
		foreach ($sections as $label => $questions) {
			$fieldset = $form->appendChild($response->createFieldset($label));
			// process each of the questions in this section
			foreach ($questions as $code) {
				$fieldset->appendChild(self::formFieldForQuestionCode($response, $questionnaire, $code, $view, self::valueForQuestionCode($questionnaire, $object, $code)));
			}
		}
		
		// add the form controls
		if ($view == 'create') {
			$controls = $form->appendChild($response->createDiv(null, array('class' => 'controls')));
			$controls->appendChild($response->createSubmitButtonInput($view.'[save]', 'Create '.$object->modelLabel));
		} else {
			$form->appendChild(self::standardFormControls($response, $view));
		}
		return $form;
	}
	public static function formFieldForQuestionCode(ELSWAK_HTML_Response $response, ELSWAK_ManagedObject_Questionnaire_Abstract $questionnaire, $code, $namePrefix = 'question', $value = null) {
		// determine if the question exists
		if (($question = $questionnaire->questionForCode($code)) !== false) {
			// ensure every question is an array
			if (!is_array($question)) {
				$question = array('label' => $question);
			}
			// setup automatic ids for top level questions
			if (!array_key_exists('field-attributes', $question)) {
				$question['field-attributes'] = array();
			}
			if (!array_key_exists('id', $question['field-attributes'])) {
				$question['field-attributes']['id'] = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($code))));
			}
			return self::formFieldForQuestionDataWithCode($response, $question, $code, $namePrefix, $value);
		}
		throw new ELSWAK_ManagedObject_HTMLHandler_Exception('Unable to create form field. Invalid question code provided.');
	}
	public static function formFieldForQuestionDataWithCode(ELSWAK_HTML_Response $response, $question, $code, $namePrefix = 'question', $value = null) {
		// determine if the question has a type
		if (is_array($question)) {
			// setup the common attributes
			$label = null;
			if (array_key_exists('label', $question)) {
				$label = $question['label'];
			} else {
				$label = ucwords(strtolower(str_replace('-', ' ', $code)));
			}
			
			// input attributes
			$attributes = array();
			if (array_key_exists('attributes', $question)) {
				$attributes = $question['attributes'];
			}
			if (array_key_exists('class', $question)) {
				$attributes = $response->addClassToAttributesArray($question['class'], $attributes);
			}
			if (array_key_exists('size', $question)) {
				$attributes['size'] = $question['size'];
			}
			if (array_key_exists('rows', $question)) {
				$attributes['rows'] = $question['rows'];
			}
			if (array_key_exists('cols', $question)) {
				$attributes['cols'] = $question['cols'];
			}
			if (array_key_exists('title', $question)) {
				$attributes['title'] = $question['title'];
			}
			
			// field attributes
			$fieldAttributes = array();
			if (array_key_exists('field-attributes', $question)) {
				$fieldAttributes = $question['field-attributes'];
			}
			if (array_key_exists('field class', $question)) {
				$fieldAttributes = $response->addClassToAttributesArray($question['field class'], $fieldAttributes);
			}
			if (array_key_exists('id', $question)) {
				$fieldAttributes['id'] = $question['id'];
			}
			$description = null;
			if (array_key_exists('description', $question)) {
				$description = $question['description'];
			}
			
			// process the field based on type
			if (array_key_exists('type', $question)) {
				if ($question['type'] == 'text') {
					return self::formFieldForTextQuestion($response, $namePrefix, $code, $label, $value, $description, $attributes, $fieldAttributes);
				} else if ($question['type'] == 'select' && array_key_exists('values', $question)) {
					return self::formFieldForSelectQuestion($response, $namePrefix, $code, $label, $value, $question['values'], $description, $attributes, $fieldAttributes);
				} else if ($question['type'] == 'radio' && array_key_exists('values', $question)) {
					return self::formFieldForRadioQuestion($response, $namePrefix, $code, $label, $value, $question['values'], $description, $attributes, $fieldAttributes);
				} else if ($question['type'] == 'checkbox' && array_key_exists('values', $question)) {
					return self::formFieldForCheckboxQuestion($response, $namePrefix, $code, $label, $value, $question['values'], $description, $attributes, $fieldAttributes);
				} else if ($question['type'] == 'yes/no') {
					$fieldAttributes = $response->addClassToAttributesArray('yes-no', $fieldAttributes);
					return self::formFieldForYesNoQuestion($response, $namePrefix, $code, $label, $value, $description, $attributes, $fieldAttributes);
				} else if ($question['type'] == 'person') {
					return self::formFieldForPersonQuestion($response, $namePrefix, $code, $label, $value, $description, $attributes, $fieldAttributes);
				} else if (array_key_exists('item', $question)) {
					if ($question['type'] == 'list') {
						$fieldAttributes = $response->addClassToAttributesArray('list', $fieldAttributes);
						return self::formFieldForListQuestion($response, $namePrefix, $code, $label, $question['item'], $value, $description, $attributes, $fieldAttributes);
					}
					return self::formFieldForItemQuestion($response, $namePrefix, $code, $label, $question['item'], $value, $description, $attributes, $fieldAttributes);
				} else if ($question['type'] == 'html' && array_key_exists('content', $question)) {
					return $response->createFormField($label, $response->convertHTML($question['content']), null, $fieldAttributes);
				} else {
					return self::formFieldForStringQuestion($response, $namePrefix, $code, $label, $value, $description, $attributes, $fieldAttributes);
				}
			} else {
				return self::formFieldForStringQuestion($response, $namePrefix, $code, $label, $value, $description, $attributes, $fieldAttributes);
			}
		} else if (is_string($question)) {
			// treat the question as simply a label for a plain text field
			return self::formFieldForStringQuestion($response, $namePrefix, $code, $question, $value);
		}
		throw new ELSWAK_ManagedObject_HTMLHandler_Exception('Unable to create form field. Invalid question selected.');
	}
	public static function valueForQuestionCode(ELSWAK_ManagedObject_Questionnaire_Abstract $questionnaire, ELSWAK_ManagedObject $object, $code) {
		if (($question = $questionnaire->questionForCode($code)) !== false) {
			if (is_array($question) && array_key_exists('property', $question)) {
				return $object->{$question['property']};
			} else if (method_exists($object, 'responseForQuestion')) {
				return $object->responseForQuestion($code);
			}
		}
		return null;
	}
	public static function formFieldForStringQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, $value = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		return $response->createFormField($label, $response->createTextInput($namePrefix.'['.$code.']', $value, $attributes), $description, $fieldAttributes);
	}
	public static function formFieldForTextQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, $value = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		if ($attributes == null) {
			$attributes = array();
		}
		if (empty($attributes['rows'])) {
			$attributes['rows'] = 3;
		}
		if (empty($attributes['cols'])) {
			$attributes['cols'] = 40;
		}
		return $response->createFormField($label, $response->createTextArea($namePrefix.'['.$code.']', $value, $attributes), $description, $fieldAttributes);
	}
	public static function formFieldForSelectQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, $value = null, array $values = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		return $response->createFormField($label, $response->createSelect($namePrefix.'['.$code.']', $value, $values, 'Select '.$label, $attributes), $description, $fieldAttributes);
	}
	public static function formFieldForRadioQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, $value = null, array $values = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		$input = $response->createDiv(null, $response->addClassToAttributesArray('input', $attributes));
		foreach ($values as $key => $text) {
			$match = false;
			if (
				$value == $key ||
				$value == $text
			) {
				$match = true;
			}
			$input->appendChild($response->createLabeledRadioInput($namePrefix.'['.$code.']', $key, $text, $match));
		}
		return $response->createFormField($label, $input, $description, $fieldAttributes);
	}
	public static function formFieldForCheckboxQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, $value = null, array $values = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		$input = $response->createDiv(null, $response->addClassToAttributesArray('input', $attributes));
		foreach ($values as $key => $text) {
			$match = false;
			if (
				is_array($value) && array_key_exists($key, $value) && (
					$value[$key] == $key ||
					$value[$key] == $text
				)
			) {
				$match = true;
			}
			$input->appendChild($response->createLabeledCheckboxInput($namePrefix.'['.$code.']['.$key.']', $key, $text, $match));
		}
		return $response->createFormField($label, $input, $description, $fieldAttributes);
	}
	public static function formFieldForYesNoQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, $value = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		// create a standard test value for ease of comparison
		$testValue = strtoupper(substr($value, 0, 1));
		$input = $response->createDiv(null, $response->addClassToAttributesArray('input', $attributes));
		$input->appendChild($response->createLabeledRadioInput($namePrefix.'['.$code.']', 'YES', 'Yes', $testValue == 'Y'));
		$input->appendChild($response->createLabeledRadioInput($namePrefix.'['.$code.']', 'NO', 'No', $testValue == 'N'));
		return $response->createFormField($label, $input, $description, $fieldAttributes);
	}
	public static function formFieldForPersonQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, DataGeneral_Person $value = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		$container = $response->createDiv(null, $response->addClassToAttributesArray('input', $attributes));
		if ($value instanceof DataGeneral_Person) {
			$container->appendChild($response->createLabel($value));
			$container->appendChild($response->createHiddenInput($namePrefix.'['.$code.']', $value->id, $response->addClassToAttributesArray('person picker', $attributes)));
		} else {
			$container->appendChild($response->createLabel('Please wait while the person picker loads.'));
			$container->appendChild($response->createHiddenInput($namePrefix.'['.$code.']', $value, $response->addClassToAttributesArray('person picker', $attributes)));
		}
		return $response->createFormField($label, $container, $description, $fieldAttributes);
	}
	public static function formFieldForItemQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, array $itemSchema, $value = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		$input = $response->createDiv(null, array('class' => 'input'));
		
		// process each item field adding the appropriate form field to this field
		foreach ($itemSchema as $property => $definition) {
			$propertyValue = null;
			if (isset($value[$property])) {
				$propertyValue = $value[$property];
			}
			$input->appendChild(self::formFieldForQuestionDataWithCode($response, $definition, $property, $namePrefix.'['.$code.']', $propertyValue));
		}
		
		return $response->createFormField($label, $input, $description, $fieldAttributes);
	}
	public static function formFieldForListQuestion(ELSWAK_HTML_Response $response, $namePrefix, $code, $label, array $itemSchema, $value = null, $description = null, array $attributes = null, array $fieldAttributes = null) {
		$input = $response->createElement('ul', null, array('class' => 'input'));
		
		// process each item field adding the appropriate form field to this field
		if (!is_array($value) || count($value) == 0) {
			$value = array();
		}
		foreach ($value as $key => $item) {
			$itemIsArray = is_array($item);
			$li = $input->appendChild($response->createLi());
			foreach ($itemSchema as $property => $definition) {
				$propertyValue = null;
				if ($itemIsArray && array_key_exists($property, $item)) {
					$propertyValue = $item[$property];
				}
				$li->appendChild(self::formFieldForQuestionDataWithCode($response, $definition, $property, $namePrefix.'['.$code.']['.$key.']', $propertyValue));
			}
		}
		// add the default item last
		$li = $input->appendChild($response->createLi(null, array('class' => 'default')));
		foreach ($itemSchema as $property => $definition) {
			$li->appendChild(self::formFieldForQuestionDataWithCode($response, $definition, $property, $namePrefix.'['.$code.'][default]'));
		}
		
		return $response->createFormField($label, $input, $description, $fieldAttributes);
	}




// ========================= 
// !Standard Form Controls   
// ========================= 
	public static function standardFormControls(ELSWAK_HTML_Response $response, $namePrefix) {
		$controls = $response->createDiv(null, array('class' => 'controls'));
		$controls->appendChild($response->createSubmitButtonInput($namePrefix.'[save]', 'Save'));
		$controls->appendChild($response->createSubmitButtonInput($namePrefix.'[continue]', 'Save & Continue'));
		$controls->appendChild($response->createSubmitButtonInput($namePrefix.'[cancel]', 'Undo Changes'));
		return $controls;
	}
// ========================== 
// !Form Control Detection   
// ========================== 
	public static function shouldLoadChanges($namePrefix) {
		// only load the form data if a valid form control was pressed
		return self::userRequestedSave($namePrefix);
	}
	public static function shouldContinueToNextStep($namePrefix) {
		if (isset($_POST[$namePrefix]))
			if (isset($_POST[$namePrefix]['continue']))
				return true;
		return false;
	}
	public static function userRequestedSave($namePrefix) {
		// look in the named form to determine if a save request was issued
		if (isset($_POST[$namePrefix])) {
			if (isset($_POST[$namePrefix]['save']))
				return true;
			if (isset($_POST[$namePrefix]['continue']))
				return true;
			if (isset($_POST[$namePrefix]['create']))
				return true;
		}
		return false;
	}
	public static function userRequestedCancel($namePrefix) {
		// look in the named form to determine if a cancel request was issued
		if (isset($_POST[$namePrefix]))
			if (isset($_POST[$namePrefix]['cancel']))
				return true;
		return false;
	}
	public static function userRequestedDelete($namePrefix) {
		// look in the named form to determine if a delete request was issued
		if (isset($_POST[$namePrefix]))
			if (isset($_POST[$namePrefix]['delete']))
				return true;
		return false;
	}
	public static function userRequestedExport($namePrefix) {
		if (isset($_POST[$namePrefix]))
			if (isset($_POST[$namePrefix]['export']))
				return true;
		return false;
	}
// ===================== 
// !Confirmation Code   
// ===================== 
	public static function confirmationCode($namePrefix) {
		if (isset($_POST[$namePrefix])) {
			if (isset($_POST[$namePrefix]['confirm'])) {
				// create a new Zend filter to strip out HTML and such
				$tags = new Zend_Filter_StripTags;
				return $tags->filter($_POST[$namePrefix]['confirm']);
			}
		}
		throw new Exception('Unable to load confirmation code from form data. No inputs with given prefix exist.');
	}
}