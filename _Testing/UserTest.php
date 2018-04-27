<?php
class BadUser extends ELSWAK_User {
	public static function keyFactor() {
		return 8;
	}
}

class ELSWAK_UserTest
	extends PHPUnit\Framework\TestCase {
	
	public function testConstructor() {
		$user = new ELSWAK_User('tester', null, 'Test E. Tester');
		$this->assertInstanceOf('ELSWAK_User', $user);
		return $user;
	}

	public function testDefaultPasswordVerification() {
		// by default the password hash should be blank and nothing should match
		$user = new ELSWAK_User;
		$this->assertFalse($user->verifyPassword(null));
		$this->assertFalse($user->verifyPassword('ASDF'));
	}
	
	public function testPassword() {
		// create a new user with a password hash that matches the password 'asdf1234'
		$user = new ELSWAK_User('tester', '$2y$09$FijzdwC7Z17zkEIHcOIiCOn8SU2Dk149IG4HZVcDUUmlEMOaTztAa');
		$this->assertTrue($user->verifyPassword('asdf1234'));
		$this->assertFalse($user->verifyPassword('ASDF1234'));
		$user->setPassword('ASDF1234');
		$this->assertTrue($user->verifyPassword('ASDF1234'));
		return $user;
	}
	
	public function testIdentifier() {
		$user = new ELSWAK_User('testy');
		$this->assertEquals('2b923f57a4603fc73ab182b3f93567f2', $user->identifier);
	}
	
	/**
	 * @expectedException ELSWAK_User_InvalidKeyFactor_Exception
	 */
	public function testBadKeyFactor() {
		$user = new BadUser;
		$user->setPassword('asdf');
	}
}
