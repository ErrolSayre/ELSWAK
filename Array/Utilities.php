<?php
/*
	ELSWAK Array Utilities
	
	This class was created to provide a place to put these various join methods.
*/
class ELSWAK_Array_Utilities {
	const SPACE = ' ';
	
	public static function joinWithOptions(array $items, $conjunction, $useOxfordComma, $separator) {
		// utilize some index values since the array will need to be iterated by foreach to support associative arrays
		$i = 0;
		$count = count($items);
		$threshold = $count - 2;
		$list = '';
		foreach ($items as $item) {
			// determine if this item is before second to last
			if ($i < $threshold) {
				$list .= $item.$separator;
			} else if ($i < $count - 1) {
				// the item is second to last
				// determine if we should include a comma (assume the caller dis/enables this as appropriate if not using commas)
				if ($count > 2 && $useOxfordComma) {
					$list .= $item.$separator.ltrim($conjunction);
				} else if ($count == 2 && !$conjunction) {
					// in the case of a pair with no conjunction, default to using the separator
					$list .= $item.$separator;
				} else {
					$list .= $item.$conjunction;
				}
			} else if ($i < $count) {
				$list .= $item;
			}
			++$i;
		}
		return $list;
	}
	public static function joinToEnglishListing(array $items, $conjunction = 'and', $useOxfordComma = true, $separator = ',') {
		// make some sensible choices regarding formatting
		// the caller can use the main method directly if other options are required
		
		// ensure the conjunction is either AND or OR
		$trimmed = trim($conjunction);
		$compare = strtolower($trimmed);
		$preferred = self::preferredConjunctions();
		if (in_array($compare, $preferred)) {
			$conjunction = self::SPACE.$trimmed.self::SPACE;
		} else {
			$conjunction = false;
		}
		
		// ensure the separator has proper spacing
		$trimmed = trim($separator);
		$preferred = self::preferredSeparators();
		if (in_array($trimmed, $preferred)) {
			$separator = $trimmed.self::SPACE;
		} else {
			$separator = array_shift($preferred).self::SPACE;
		}
		
		// ensure a boolean value for oxford comma
		// the valueAsBoolean method will convert strings like 'y', 'yes', 'true' to a true value
		$useOxfordComma = ELSWAK_Settable::valueAsBoolean($useOxfordComma);
		
		return self::joinWithOptions($items, $conjunction, $useOxfordComma, $separator);
	}
	public static function preferredConjunctions() {
		return array(
			'and',
			'or',
		);
	}
	public static function preferredSeparators() {
		return array(
			',',
			';',
		);
	}
}