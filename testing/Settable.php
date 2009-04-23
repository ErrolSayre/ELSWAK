<?php
require_once 'ELSWebAppKit/Settable.php';
class example
	extends ELSWebAppKit_Settable
{
	protected $id;
	protected $title;
	protected $name;
	protected $email;
	protected $date;
	protected $gettable;
	protected $settable;
	
	public function setId($value)
	{
		return $this->_setPropertyAsId('id', $value);
	}
	public function setName($name)
	{
		$this->name = strtoupper($name);
	}
	public function email()
	{
		// return the email address as a mailto link
		return str_replace('@', ' (at) ', $this->email);
	}
	public function emailLink()
	{
		return 'mailto:'.$this->email;
	}
	public function date($format = null)
	{
		if ($format !== null)
			return $this->_getPropertyAsDate('date', $format);
		return $this->date;
	}
	public function setDate($value)
	{
		return $this->_setPropertyAsTimestamp('date', $value);
	}
	protected function setGettable($value)
	{
		$this->gettable = $value;
		return $this;
	}
	protected function settable()
	{
		return $this;
	}
}


echo '<h1>Creating var1</h1>'.LF;
$var1 = new example();

echo '<h2>Setting id to 230</h2>'.LF;
try { $var1->id = 230; echo $var1->id.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting title to "Dr."</h2>'.LF;
try { $var1->title = 'Dr.'; echo $var1->title.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting name to "John SMith"</h2>'.LF;
try { $var1->name = 'John SMith'; echo $var1->name.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting email to "joe@theplace.com"</h2>'.LF;
try { $var1->email = 'joe@theplace.com'; echo $var1->email.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting emailLink to "joe@theplace.com"</h2>'.LF;
try { $var1->emailLink = 'joe@theplace.com'; echo $var1->emailLink.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting emailLink() as property</h2>'.LF;
try { echo $var1->emailLink.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Setting date to "'.date('Y-m-d').'"</h2>'.LF;
try { $var1->date = date('Y-m-d'); echo $var1->date.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting date as property</h2>'.LF;
try { echo $var1->date.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Getting date() as datetime</h2>'.LF;
try { echo $var1->date('Y-m-d H:i:s').BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h2>Resulting object</h2>'.LF;
print_r_html($var1);

echo '<h2>Exporting object to array</h2>'.LF;
print_r_html($var1->_export);

echo '<h1>Creating var2 by import of associative array</h1>'.LF;
$var2 = new example();
$var2->_import(array('date' => time(), 'name' => 'George McDudal', 'settable' => 'Your mom', 'gettable' => 'Horray'));

echo '<h2>Resulting object</h2>'.LF;
print_r_html($var2);

echo '<h2>Exporting object to array</h2>'.LF;
print_r_html($var2->_export);
/*
echo '<h1>Creating var3 via factory method with associative array</h1>'.LF;
$var3 = example::_factory(array('date' => time(), 'name' => 'Another Person', 'settable' => 'Is it really settable?', 'gettable' => 'Doubt it’s gettable'));
*/