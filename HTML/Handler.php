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
		// setup a value shortcut
		$modelClass = strtolower($record->modelLabel);
		
		// create the container
		$attributes = $response->addClassToAttributesArray($modelClass.' navigation bar', $attributes);
		$container = $response->createDiv($response->createElement('h1', $record->label), $attributes);
		
		// process the various tasks to create a listing
		$list = $container->appendChild($response->createUl(null, array('class' => $modelClass.' task container')));
		
		// add each section as a subordinate list
		$sections = array('view', 'edit', 'existence');
		foreach ($sections as $section) {
			$sectionList = $response->createUl(null, array('class' => $modelClass.' '.$section.' tasks'));
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