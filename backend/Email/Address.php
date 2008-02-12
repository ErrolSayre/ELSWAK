<?php
/*
	ELSWebAppKit Email Address
*/
require_once('ELSWebAppKit/Iterable/Model.php');
class ELSWebAppKit_Email_Address
	extends ELSWebAppKit_Iterable
{
	const EMAIL_ADDRESS_REGX_BASE = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i';
	const EMAIL_ADDRESS_REGX = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';
	
	protected $address;
	
	// member listing for iterator methods
	protected $members = array
	(
		'address'
	);
	
	public function __construct($address = null)
	{
		// set the address
		$this->setAddress($address);
	}
	public function address()
	{
		return $this->address;
	}
	public function setAddress($address)
	{
		// verify that this is a valid email address
		if ($address !== null)
		{
			if (self::verifyEmail($address))
			{
				// this is a validly formatted email address
				$this->address = $address;
			}
			else
			{
				// this is an invalid email address
				// leave the current value alone
				throw new Exception('Invalid Email Address: String does not match email address specifications.');
			}
		}
		else
		{
			// set the address to null
			$this->address = null;
		}
	}
	public static function verifyEmail($address)
	{
		if (preg_match(self::EMAIL_ADDRESS_REGX, $address))
		{
			return true;
		}
		return false;
	}
	public function __toString()
	{
		return $this->address;
	}
}