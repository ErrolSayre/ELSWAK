<?php
/*
	ELSWebAppKit Controller Interface
	
	This interface provides a common ancestor to controllers, which must provide a run method to perform their actions upon the response before it is sent.
*/
interface ELSWebAppKit_Controller_Interface
{
	public function run(ELSWebAppKit_HTTP_Response $response, array $arguments);
		// handles the main application function of the controller, taking the response and modifying with with appropriate input from the uri arguments and the request inputs
	public static function uriComponent();
		// responds with the uri component that corresponds to the controller (for example int he uri /application/person/id the person component of that uri looks to the user as a directory but corresponds to the person controller of the application
	public static function systemComponent();
		// a human (and machine) readible label for the object/class/activity/component that the controller manages (e.g. the person controller manages "persons")
}
?>