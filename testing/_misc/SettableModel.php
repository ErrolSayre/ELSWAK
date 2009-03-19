<?php
require_once 'ELSWebAppKit/Settable/Model.php';
class class1
	extends ELSWebAppKit_Settable_Model
{
	protected $property1;
	protected $property2;
	protected $property3;
	protected $property4;
	protected $property5;
	
	public function property1()
	{
		return $this->property1;
	}
	public function setProperty1($value)
	{
		$this->property1 = $value;
		return $this;
	}
	protected function property2()
	{
		return $this->property2;
	}
	protected function setProperty2($value)
	{
		$this->property2 = $value;
		return $this;
	}
	public function setProperty4($value)
	{
		$this->property4 = intval($value);
	}
	public function property5()
	{
		return 'Property 5 is '.$this->property5;
	}
}

$var1 = new class1();
echo '<h4>Setting Property1 to "qwer"</h4>'.LF;
try { $var1->property1 = 'qwer'; echo $var1->property1.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }
echo '<h4>Setting Property1 to "qwer method" using method</h4>'.LF;
try { $var1->setProperty1('qwer method'); echo $var1->property1().BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h4>Setting Property2 to "asdf"</h4>'.LF;
try { $var1->property2 = 'asdf'; echo $var1->property2.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h4>Setting Property3 to "zxcv"</h4>'.LF;
try { $var1->property3 = 'zxcv'; echo $var1->property3.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h4>Setting Property4 to "wert"</h4>'.LF;
try { $var1->property4 = 'wert'; echo $var1->property4.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h4>Setting Property5 to "sdfg"</h4>'.LF;
try { $var1->property5 = 'sdfg'; echo $var1->property5.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h4>Setting Property6 to "xcvb"</h4>'.LF;
try { $var1->property6 = 'xcvb'; echo $var1->property6.BR.LF; } catch (Exception $e) { echo $e->getMessage().BR.LF; }

echo '<h4>Resulting Object</h4>'.LF;
print_r_html($var1);