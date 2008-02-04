<?php
/*
	ELSWebAppKit MySQL Expression
	
	This class provides a hierarchy for MySQL conditional statements and other
	such expressions.
*/
interface ELSWebAppKit_MySQL_Expression
{
	public function sql($format = '', $indent = '');
}
?>