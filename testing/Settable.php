<?php
require_once 'ELSWebAppKit/Settable.php';
class example
	extends ELSWebAppKit_Settable
{
	protected $id;
	protected $title;
	protected $name;
	protected $email;
	
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

echo '<h2>Resulting object</h2>'.LF;
print_r_html($var1);