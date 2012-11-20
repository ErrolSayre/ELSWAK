<?php
/*
# ELSWAK User

Provides a generic yet usable user base class. A typical application might extend this class to insert a person object or a numeric database id.

Since this generic class is expecting to be coupled with a file or cache based store, it includes the userHash() method to provide a non-numeric identifier.

## Password Hash

Rather than storing a plain-text password (or even a reversably encrypted password) this class expects that you'll need to store passwords in a potentially vulnerable position. This class utilizes PHP 5.3.7’s Blowfish support within the crypt() function.

Since crypt() returns the salt as a part of the value but simply fills in salt space with '$', this class generates a random salt each time a password hash is generated.

*/

class ELSWAK_User_Exception extends ELSWAK_Exception {}
class ELSWAK_User_InvalidKeyFactor_Exception extends ELSWAK_User_Exception {}

class ELSWAK_User
	extends ELSWAK_Settable {
	
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
/*
	Hash the password with a random salt; peppering with the user account name.
*/
	protected function generatePasswordHash($password) {
		// generate the salt
		$salt = '';
		$alphabet = $this->saltAlphabet();
		$alphabetLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 22; ++$i) {
			$salt .= $alphabet[rand(0, $alphabetLength)];
		}
		
		// utilize the blowfish encryption guaranteed to be present in PHP 5.3 and later
		$hash = crypt($this->pepperPassword($password), '$2y$'.$this->keyFactor().'$'.$salt);
		
		// ensure the hash is valid (exactly 60 characters for Blowfish)
		if (strlen($hash) == 60) {
			return $hash;
		}
		throw new ELSWAK_User_InvalidKeyFactor_Exception('Unable to generate proper password hash. Please verify supplied key factor.');
	}
	protected function pepperPassword($password) {
		// for now just pepper the password by appending the account name
		return $this->account.$password;
	}
	public function verifyPassword($password) {
		// compare the hash of the input to the current hash
		// since the crypt function includes the appropriate metadata in the hash it can be passed as the salt
		if ($this->passwordHash == crypt($this->pepperPassword($password), $this->passwordHash)) {
			return true;
		}
		return false;
	}
	
	

/* !ELSWAK Pseudo-Property Accessors */	
	public function identifier() {
		return md5($this->account);
	}
	
	
	
/* !Static Methods */
	public static function keyFactor() {
/*
	Return a valid key factor for Blowfish.
	Override this method in subclass to provide an alternate 2 digit integer from 04 to 31. The recommended value should quickly enough for the system to be responsive but long enough to slowdown a brute-force attack to unreasonable times.
*/
		return '09';
	}
	public static function saltAlphabet() {
		// return valid characters for use within the random salt
		return './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	}
}