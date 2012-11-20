<?php
/*
# ELSWAK User

Provides a generic yet usable user base class. A typical application might extend this class to insert a person object or a numeric database id.

Since this generic class is expecting to be coupled with a file or cache based store, it includes the userHash() method to provide a non-numeric identifier.

## Password Hash

Rather than storing a plain-text password (or even a reversably encrypted password) this class expects that you'll need to store passwords in a potentially vulnerable position. This class utilizes PHP 5.3.7’s Blowfish support within the crypt function. By hashing the password with fairly immutable user attributes (account name), none of the hashes stored are directly related to each other and thus each would have a unique rainbow table. The underlying mechanism utilizes an extremely high number of iterations to provide further entropy to the hash.

## A note about the hash salt.


The hash salt is stored within this class as a static attribute to aid in global configuration. It is highly recommended that this value be overridden by subclasses (perhaps as a static method call below the class definition) to provide extra entropy within the local application. This should happen before the class is ever actually used so that any generated data is not invalidated. Please note that this must be a 22 digit string made up of only alphanumeric characters and period and forward slash. Alternatively the salt could be stored within the database but this class assumes it is adequate to use a global salt peppered with user details and stored separately from the user records.

Please also note that the password hash will change if the user’s account is renamed. This is easy to deal with if prompting the user for password confirmation when changing the account name.

*/
class ELSWAK_User
	extends ELSWAK_Settable {
	
	// configuration values (see notes above before overriding)
	// Key factor passed to Blowfish algorithmeter —must be a 2 digit integer between 04-31
	public static $keyFactor = '09';
	// Salt passed to crypt —must be a 22 character string made of the characters A-z0-9./
	//                         '<-- 22 characters --->'
	public static $salt      = 'OverrideThisSaltPlease';
	
	protected $account;
	protected $passwordHash;
	protected $displayName;
	
	public function __construct($account = 'user', $passwordHash = '', $displayName = 'Unnamed User') {
		if (is_array($account) || is_object($account)) {
			$this->_import($account);
		} else {
			$this->setAccount($account);
			$this->setPasswordHash($passwordHash);
			$this->setDisplayName($displayName);
		}
	}
	
/* !Property Accessors */
	public function setAccount($value) {
		$this->account = strval($value);
		return $this;
	}
	public function setDisplayName($value) {
		$this->displayName = strval($value);
		return $this;
	}
/* !Password Methods */
	public function setPasswordHash($value) {
		$this->passwordHash = strval($value);
		return $this;
	}
	public function setPassword($password) {
		$this->passwordHash = $this->generatePasswordHash($password);
		return $this;
	}
	protected function generatePasswordHash($password) {
		// hash the password with the class salt; peppering with the user account name
		// utilize the blowfish encryption guaranteed to be present in PHP 5.3 and later
		return crypt($this->account.$password, '$2y$'.self::$keyFactor.'$'.self::$salt.'$');
	}
	public function verifyPassword($password) {
		if ($this->passwordHash == $this->generatePasswordHash($password)) {
			return true;
		}
		return false;
	}
	
	

/* !ELSWAK Pseudo-Property Accessors */	
	public function identifier() {
		return md5($this->account.self::$salt);
	}
	
	
	
/* !Static Methods */
	public static function setSalt($salt) {
		self::$salt = strval($salt);
	}
}