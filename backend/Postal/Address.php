<?php
/*
	ELSWebAppKit Postal Address
*/
require_once('ELSWebAppKit/Iterable.php');
class ELSWebAppKit_Postal_Address
	extends ELSWebAppKit_Iterable
{
	protected $lines;
	protected $city;
	protected $state;
	protected $postal;
	protected $country;
	
	// member listing for iterator methods
	protected $_iterables = array
	(
		'lines',
		'city',
		'state',
		'postal',
		'country'
	);
	
	public function __construct($line1 = '', $line2 = '', $city = '', $state = '', $postal = '', $country = '')
	{
		$this->setAddress($line1, $line2, $city, $state, $postal, $country);
	}
	public function address($format = 'plain-text')
	{
		// determine the line separator
		$lineSeparator = LF;
		$format = strtolower($format);
		if ($format == 'single-line')
		{
			$lineSeparator = ', ';
		}
		else if ($format == 'html')
		{
			$lineSeparator = BR;
		}
		
		// assemble the address line by line
		$address = '';
		foreach ($this->lines as $line)
		{
			$address .= $line.$lineSeparator;
		}
		
		// add the city, state zip as best fits
		if (($this->city != '')		&&
			($this->state != '')	&&
			($this->postal != ''))
		{
			$address .= $this->city.', '.$this->state.' '.$this->postal.$lineSeparator;
		}
		else if (($this->city != '')	&&
			($this->postal != ''))
		{
			$address .= $this->city.' '.$this->postal.$lineSeparator;
		}
		else if ($this->city != '')
		{
			$address .= $this->city.$lineSeparator;
		}
		if ($this->country != '')
		{
			$address .= $this->country.$lineSeparator;
		}
		
		// remove the trailing line separator
		$address = substr($address, 0, -1 * strlen($lineSeparator));
		
		// trim off the excess whitespace and send the address back
		return trim($address);
	}
	public function setAddress($line1, $line2, $city, $state, $postal, $country)
	{
		// reset the lines array
		$this->lines = null;
		$this->addLine($line1);
		$this->addLine($line2);
		
		// set the other fields appropriately
		$this->setCity($city);
		$this->setState($state);
		$this->setPostal($postal);
		$this->setCountry($country);
		return $this;
	}
	public function line($line)
	{
		if (isset($this->lines[$line - 1]))
		{
			return $this->lines[$line - 1];
		}
		
		return '';
	}
	public function addLine($line)
	{
		if ($line != '')
		{
			$this->lines[] = $line;
		}
	}
	public function lineCount()
	{
		return count($this->lines);
	}
	public function lines()
	{
		return $this->lines;
	}
	public function city()
	{
		return $this->city;
	}
	public function setCity($city)
	{
		$this->city = $city;
		return $this;
	}
	public function state()
	{
		return $this->state;
	}
	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}
	public function postal()
	{
		return $this->postal;
	}
	public function setPostal($postal)
	{
		$this->postal = $postal;
		return $this;
	}
	public function country()
	{
		return $this->country;
	}
	public function setCountry($country)
	{
		$this->country = $country;
		return $this;
	}
	public function __toString()
	{
		return $this->address('single-line');
	}
}
?>