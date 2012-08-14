<?php
/*
# ELSWAK User

Provides a generic yet usable user base class. A typical application might extend this class to insert a person object or a numeric database id.

Since this generic class is expecting to be coupled with a file or cache based store, it includes the userHash() method to provide a non-numeric identifier.

## Password Hash

Rather than storing a plain-text password (or even a reversably encrypted password) this class expects that you'll need to store passwords in a potentially vulnerable position. This class utilizes SHA512 as an irreversible hashing mechanism including user account name and a separate class-wide salt string to add further entropy. By hashing the password with fairly immutable user attributes, none of the hashes stored are directly related to each other and are thus each would have a unique rainbow table. Additionally this process is iterated several thousand times to provide further entropy.

## A note about the hash salt.

In order to prevent the user and password hashes from having a readily discernable value, this string is used to provide some configurable salting to the hashes. It is highly recommended that this value be overridden by subclasses (perhaps as a static method call below the class definition) to provide extra entropy within the local application. This should happen before the class is ever actually used so that any generated data is not invalidated. Please also note that stored hashes will need to be updated at any point this value needs to change. A side-effect of this could be a mass password invalidation simply by changing the salt —a mechanism to ease resetting one’s password would be recommended.

Please also note that the passwordHash will change if the user's account is renamed. This is easy to deal with if prompting the user for password confirmation when changing the account name.
*/
class ELSWAK_User
	extends ELSWAK_Settable {
	
	public static $salt = 'OverrideThisValueInSubclasses';
	
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
		// take the password and hash it up a few thousand times
		$hash = hash('sha512', $password.''.self::$salt.''.$this->account);
		for ($i = 0; $i < 4343; ++$i) {
			$hash = hash('sha512', $password.''.self::$salt.''.$hash.$this->account);
		}
		return $hash;
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