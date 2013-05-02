<?php
/**
 * Wrap PHP's error_log in a class that is readily configurable.
 *
 * This class will store messages until they are flushed (sent to
 * error_log) but will remove them at that time. If the logger is given
 * a message type that it is set to not display, it will not include
 * them in the flush and will keep them until desctruction. This is
 * primarily to support using one logger mechanism across dev, test, and
 * production environments.
 *
 * Please note that this logger does not register itself as an error
 * handler automatically as it is not currently targetted at that role.
 *
 * @package ELSWAK\Log
 */
class ELSWAK_Logger
	extends ELSWAK_Object
	implements ELSWAK_Logger_Interface {

//!Class properties
	protected static $messageTypes;

//!Instance properties
	protected $messages;
	protected $typeDisplaySettings;
	protected $flushImmediately;
	protected $includeStats;
	protected $dateCreated;
	protected $startTime;
	
	public function __construct($displayErrors = false, $displayMessages = false, $flushImmediately = true, $includeStats = true) {
		// set some statistics properties
		$this->startTime = microtime(true);
		$this->dateCreated = new DateTime;
		
		// set the normal properties
		$this->messages = new ELSWAK_Array;
		$this->setMessageTypeDisplay('Message', $displayMessages);
		$this->setMessageTypeDisplay('Error', $displayErrors);
		$this->setFlushImmediately($flushImmediately);
		$this->setIncludeStats($includeStats);
		
		if ($this->includeStats) {
			// add a message about starting
			$this->addMessageOfType('Logger starting at '.$this->dateCreated->format('h:i:s a l, F j, Y').'.', 'Message');
		}
	}



	/**
	 * Protect the messages store from outside interference.
	 */
	protected function setMessages() {}

	/**
	 * Add a message of a given type.
	 *
	 * Be extra forgiving and map erroneous types to the general message
	 * type, but add a message about doing so.
	 *
	 * @param string $string The message
	 * @param string $type The message type
	 * @return ELSWAK_Logger self
	 */
	public function addMessageOfType($string, $type) {
		$parsed = $this->messageTypes()->parseItem($type);
		if (!$parsed) {
			// utilize the generic message type to support subclasses with a different default/generic type
			$this->message('Invalid message type: “'.$type.'”. Adding as generic message.');
			$this->message($string);
		} else {
			// determine if the message should be sent immediately
			if ($this->flushImmediately && array_key_exists($parsed, $this->typeDisplaySettings) && $this->typeDisplaySettings[$parsed]) {
				$this->sendMessage($parsed.'-'.$this->microtimeToString().': '.$string);
			} else {
				$this->messages->setValueForKey($string, $parsed.'-'.microtime(true));
			}
		}
		return $this;
	}
	public function totalMessages() {
		return $this->messages->count();
	}

	/**
	 * Return messages collection.
	 * @param boolean $includeErrors Should errors be included?
	 * @return array
	 */
	public function messagesOfType($type) {
		// validate the type against the configured message types
		$type = $this->messageTypes()->parseItem($type);
		
		$matches = array();
		// only search if there is a valid type
		if ($type) {
			foreach ($this->messages as $key => $message) {
				if (strpos($key, $type.'-') === 0) {
					$matches[$key] = $message;
				}
			}
		}
		return $matches;
	}
	public function messages($includeErrors = false) {
		if ($includeErrors) {
			return $this->allMessages();
		}
		return $this->messagesOfType('Message');
	}
	public function allMessages() {
		return $this->messages->store();
	}
	public function message($string) {
		return $this->addMessageOfType($string, 'Message');
	}
	public function error($string) {
		return $this->addMessageOfType($string, 'Error');
	}
	/**
	 * Return only errors.
	 * @return array
	 */
	public function errors() {
		return $this->messagesOfType('Error');
	}



	/**
	 * Output each message and remove it from the queue
	 * @return ELSWAK_Logger self
	 */
	public function flush() {
		if ($this->includeStats) {
			// add a message about the flush
			$time = microtime(true) - $this->startTime;
			$now = new DateTime;
			$this->addMessageOfType('Flushing at '.$now->format('h:i:s a').' — '.round($time, 2).'s from start.', 'Message');
		}
		
		$messages = $this->messages->store();
		foreach ($messages as $key => $message) {
			// determine if this type of message should be shown
			list($type, $time) = explode('-', $key);
			if ($this->typeDisplaySettings[$type]) {
				$this->sendMessage($type.'-'.$this->microtimeToString($time).': '.$message);
				$this->messages->removeValueForKey($key);
			}
		}
	}



	protected function setTypeDisplaySettings() {}
	public function setMessageTypeDisplay($type, $value = true) {
		$this->typeDisplaySettings[$this->messageTypes()->parseItem($type)] = ELSWAK_Boolean::valueAsBoolean($value);
		return $this;
	}
	public function setDisplayErrors($value = true) {
		return $this->setMessageTypeDisplay('Error', $value);
	}
	public function setDisplayMessages($value = true) {
		return $this->setMessageTypeDisplay('Message', $value);
	}
	public function setFlushImmediately($value = true) {
		$this->flushImmediately = ELSWAK_Boolean::valueAsBoolean($value);
		return $this;
	}
	public function setIncludeStats($value = true) {
		$this->includeStats = ELSWAK_Boolean::valueAsBoolean($value);
		return $this;
	}



//!Static methods
	protected static function sendMessage($string) {
		error_log($string);
	}

	/**
	 * Provide a mechanism for including microseconds in the time stamp.
	 * @param float $time seconds with mircoseconds as decimal value
	 * @return string
	 */
	protected static function microtimeToString($time = null) {
		if (!is_numeric($time)) {
			$time = microtime(true);
		}
		$seconds = floor($time);
		$micro   = str_pad(floor(($time - $seconds) * 1000000), 6, '0', STR_PAD_LEFT);
		$date = new DateTime('@'.$seconds);
		return $date->format('i:s').'.'.$micro;
	}

	public static function setMessageTypes($messageTypes) {
		if (!($messageTypes instanceof ELSWAK_Array)) {
			$messageTypes = new ELSWAK_Array($messageTypes);
		}
		static::$messageTypes = $messageTypes;
	}
	public static function messageTypes() {
		if (!(static::$messageTypes instanceof ELSWAK_Array)) {
			static::$messageTypes = new ELSWAK_Array(array(
				'Message' => 'General Message',
				'Error'   => 'Error Message',
			));
		}
		return static::$messageTypes;
	}
}