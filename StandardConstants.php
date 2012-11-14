<?php
/*
	StandardConstants.php
	
	This file sets up helpful constants that are used throughout ELSWAK.
*/

// Text Character Constants
if (!defined('NL'))
	define('NL', "\n");
if (!defined('LF'))
	define('LF', "\n");
if (!defined('CR'))
	define('CR', "\r");
if (!defined('CRLF'))
	define('CRLF', "\r\n");
if (!defined('TAB'))
	define('TAB', "\t");
if (!defined('BR'))
	define('BR', '<br />');

// Time Constants
if (!defined('TIME_SECONDS_IN_DAY')) {
	define('TIME_SECONDS_IN_DAY', 86400);
}
if (!defined('TIME_ONE_DAY')) {
	define('TIME_ONE_DAY', TIME_SECONDS_IN_DAY);
}
if (!defined('TIME_TWO_DAYS')) {
	define('TIME_TWO_DAYS', 2 * TIME_ONE_DAY);
}
if (!defined('TIME_ONE_WEEK')) {
	define('TIME_ONE_WEEK', 7 * TIME_ONE_DAY);
}
if (!defined('TIME_ONE_YEAR')) {
	define('TIME_ONE_YEAR', 31556926);
}

// Date Formats
if (!defined('DATETIME')) {
	define('DATETIME', 'Y-m-d H:i:s');
}
