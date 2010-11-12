<?php
/*
	ELSWAK HTML Form Reader
	
	This class provides some methods that have emerged as a recurring pattern in "form reader" classes.
*/
class ELSWAK_HTML_FormReader {
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