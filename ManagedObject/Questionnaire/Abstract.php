<?php
/*
	ELSWAK Managed Object Questionnaire Abstract class
	
	This class provides the base for questionnaires used to generate forms for managed objects.
*/
abstract class ELSWAK_ManagedObject_Questionnaire_Abstract {
	
	protected static $questions;
	protected static $views;
	
	public static function questions() {
		if (empty(self::$questions)) {
			self::$questions = static::defineQuestions();
		}
		return self::$questions;
	}
	abstract protected static function defineQuestions();
	public static function questionForCode($code) {
		$questions = self::questions();
		if (array_key_exists($code, $questions)) {
			return $questions[$code];
		}
		return false;
	}
	public static function labelForQuestion($code) {
		if (($question = self::questionForCode($code)) !== false) {
			if (is_array($question) && array_key_exists('label', $question)) {
				return $question['label'];
			} else if (is_string($question)) {
				return $question;
			}
		}
		return ucwords(strtolower(str_replace('-', ' ', $code)));
	}
	public static function views() {
		if (empty(self::$views)) {
			self::$views = static::defineViews();
		}
		return self::$views;
	}
	abstract protected static function defineViews();
	public static function sectionsForView($view) {
		$views = self::views();
		if (array_key_exists($view, $views)) {
			return $views[$view];
		}
		return false;
	}
}