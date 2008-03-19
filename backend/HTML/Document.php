<?php
/*
	ELSWebAppKit HTML Document
	
	This class defines an extension of the DOMDocument that provides generic helpful features specific to HTML. It is intended to be used with ELSWebAppKit HTML Response Container but can be used on its own.
	
	This extension to the DOMDocument provides locateElementById as a replacement for the HTML specific getElementById because PHP only recognized the id attribute for elements that existed in the document at the time of the last validation (or on initial load of an HTML file). This method caches ALL ids that it finds, but whenever it doesn't have a cached reference for a given id, it searches the entire document tree. Since the DOM is not ordered in any particular way it must be iterated sequentially, however since most elements are appended to existing items it is most likely that new items will be at the "bottom" of the tree. For this reason, I start the search for items at the last child of a given node and work toward the first child. Please note that you can avoid this search by creating elements through the given methods of this class, or by registering a given element with the cache using the registerElementWithIdIndex method.
*/
class ELSWebAppKit_HTML_Document
	extends DOMDocument
{
	protected $rootNode;
	protected $headNode;
	protected $bodyNode;
	protected $scripts;
	protected $stylesheets;
	protected $titleTextNode;
	protected $elementIdIndex;
	
	public function __construct($templateFile = null)
	{
		// create the DOMDocument
		parent::__construct();
		
		// load our template file
		if (($templateFile !== null) && is_file($templateFile))
		{
			$this->load($templateFile);
		}
		else
		{
			// set up the default xhtml content
			// determine the installation path
			$path = pathinfo(__FILE__);
			$this->load($path['dirname'].'/Document/Template.xhtml');
		}
		
		// setup references to generic elements
		$this->rootNode = $this->getElementsByTagName('html')->item(0);
		$this->headNode = $this->getElementsByTagName('head')->item(0);
		$this->bodyNode = $this->getElementsByTagName('body')->item(0);
		
		// collect any scripts in the template
		$this->scripts = array();
		foreach ($this->getElementsByTagName('script') as $script)
		{
			$this->scripts[] = $script;
		}
		
		// collect any stylesheets in the template
		$this->stylesheets = array();
		foreach ($this->getElementsByTagName('link') as $link)
		{
			if (strtolower($link->getAttribute('rel')) == 'stylesheet')
				$this->stylesheets[] = $link;
		}
		
		// setup the element id index
		$this->elementIdIndex = array();
	}
	public function cleanup()
	{
		return $this;
	}
	public function root()
	{
		return $this->rootNode;
	}
	public function head()
	{
		return $this->bodyNode;
	}
	public function body()
	{
		return $this->bodyNode;
	}
	public function addContent($content)
	{
		if ($content instanceof DOMElement)
		{
			return $this->bodyNode->appendChild($content);
		}
		else
		{
			return $this->bodyNode->appendChild($this->createElement('div', $content));
		}
	}
	public function addMessage($message)
	{
		return $this->addContent($message);
	}
	public function locateElementById($id)
	{
/*
	Since PHP requires that the document be verified before using the getElementById method, it is costly and painful to use that method after making changes to the DOM. To address this shortcoming this extension of the DOMDocument model provides an element id searching and caching system to replicate the functionality of getElementById.
*/
		// look for the id in the cache
		if (isset($this->elementIdIndex[$id]))
		{
			// make sure this cached reference is still good
			if ($this->elementIdIndex[$id]->getAttribute('id') == $id)
			{
				// the cached reference is good
				return $this->elementIdIndex[$id];
			}
			else
			{
				// update the reference with the new location for this object
				// save the current reference if the element has an id
				if (($otherId = $this->elementIdIndex[$id]->getAttribute('id')) != '')
				{
					$this->elementIdIndex[$otherId] = $this->elementIdIndex[$id];
				}
				
				// remove the current reference for the given id
				$this->elementIdIndex[$id] = null;
				
				// look for the element within the document's native mechanism
				if ($this->getElementById($id) !== null)
				{
					return $this->searchDomTreeForElementById($this->getElementById($id), $id);
				}
				
				// search for a matching element in the tree
				return $this->searchDomTreeForElementById($this->rootNode, $id);
			}
		}
		else
		{
			// this id hasn't been searched for yet
			return $this->searchDomTreeForElementById($this->rootNode, $id);
		}
	}
	public function searchDomTreeForElementById(DOMNode $node, $id)
	{
		// start at the root of the document and process the tree
		// determine if the current node has an id
		if (($node->nodeType == XML_ELEMENT_NODE) && $node->hasAttribute('id'))
		{
			// this node has an id
			
			// save a reference in the index
			$this->elementIdIndex[$node->getAttribute('id')] = $node;
			
			// determine if this node is the requested node
			if ($node->getAttribute('id') == $id)
			{
				// return the element
				return $node;
			}
		}
		
		// determine if the current node has children
		if ($node->hasChildNodes())
		{
			// process each child
			$currentNode = $node->lastChild;
			while ($currentNode !== null)
			{
				// search this node's tree
				$found = $this->searchDomTreeForElementById($currentNode, $id);
				
				// determine if the requested node was found
				if ($found !== null)
				{
					return $found;
				}
				
				// move on to the next node
				$currentNode = $currentNode->previousSibling;
			}
		}
		
		// the node wasn't found
		return null;
	}
	public function registerElementWithIdIndex(DOMNode $node)
	{
		// determine if the current node has an id
		if (($node->nodeType == XML_ELEMENT_NODE) && $node->hasAttribute('id'))
		{
			// this node has an id
			
			// save a reference in the index
			$this->elementIdIndex[$node->getAttribute('id')] = $node;
		}
		else
		{
			throw new Exception('Element not registered: node must be a valid element with an id attribute.');
		}
	}
	public function setPageTitle($title)
	{
		// determine if we have a reference to the title textnode
		if ($this->titleTextNode == null)
		{
			// locate the existing title tags
			$titleElements = $this->headNode->getElementsByTagName('title');
			
			// remove each
			foreach ($titleElements as $titleElement)
			{
				$this->headNode->removeChild($titleElement);
			}
			
			// create a new title
			$titleElement = $this->headNode->appendChild($this->createElement('title'));
			
			// setup the title text node
			$this->titleTextNode = $titleElement->appendChild($this->createTextNode($title));
		}
		else
		{
			// since we have a reference to the title text node we can modify its value directly
			$this->titleTextNode->nodeValue = $title;
		}
		return $this;
	}
	public function createElement($tagName, $content = null, $id = null, $class = null)
	{
		// create a new element as normal
		$element = parent::createElement($tagName);
		
		// determine if any content was provided
		if ($content instanceof DOMElement)
		{
			$element->appendChild($content);
		}
		else if ($content !== null)
		{
			$element->appendChild($this->createTextNode($content));
		}
		
		// set appropriate attributes
		if ($id !== null)
		{
			$element->setAttribute('id', $id);
		}
		if ($class !== null)
		{
			$element->setAttribute('class', $class);
		}
		
		// return the finished element
		return $element;
	}
	public function createLink($href, $label = null, $title = null, $target = null, $name = null, $id = null)
	{
		// create a new anchor element
		$a = $this->createElement('a');
		
		// add the text content
		if ($label instanceof DOMElement)
		{
			$a->appendChild($label);
		}
		else
		{
			$a->appendChild($this->createTextNode($label));
		}
		
		// set the appropriate attributes
		$a->setAttribute('href', $href);
		$a->setAttribute('title', $title);
		$a->setAttribute('href', $href);
		$a->setAttribute('name', $href);
		if ($id !== null)
		{
			$a->setAttribute('id', $id);
			
			// register this element with the id index
			$this->registerElementWithIdIndex($a);
		}
		
		// return this element
		return $a;
	}
	public function createForm($action, $method = 'POST', $content = null, $id = null, $class = null)
	{
		// create a new form element
		$form = $this->createElement('form', $content, $id, $class);
		$form->setAttribute('action', $action);
		$form->setAttribute('method', $method);
		return $form;
	}
	public function createFieldset($legend = null, $content = null, $id = null, $class = null)
	{
		// create a new fieldset element
		$fieldset = $this->createElement('fieldset', $content, $id, $class);
		if ($legend !== null)
		{
			$fieldset->appendChild($this->createElement('legend', $legend));
		}
		return $fieldset;
	}
	public function createFormField($label, $input, $description = null)
	{
/*
	A "form field" in this document is made of a "field" container, which has a "label", "input" and "description". This function accepts 3 arguments corresponding to these three attributes.
*/
		// create the field container
		$fieldContainer = $this->createElement('div');
		$fieldContainer->setAttribute('class', 'field');
		
		// add the label
		// determine if the label provided is a DOM element
		if ($label instanceof DOMElement)
		{
			// determine if this is a label
			if (strtolower($input->tagName) == 'label')
			{
				// add this element as the label for this form item
				$fieldContainer->appendChild($label);
			}
			else
			{
				// add this element as the label within a container
				$fieldContainer->appendChild($this->createElement('label', $label));
			}
		}
		else
		{
			// add the label as text to the label element
			$fieldContainer->appendChild($this->createElement('label'))->appendChild($this->createTextNode($label));
		}
		
		// add the input
		// determine if the input provided is a DOM element
		if ($input instanceof DOMElement)
		{
			// determine if this is an input
			if (strtolower($input->tagName) == 'input')
			{
				// add this element as the input for this form item
				$fieldContainer->appendChild($input);
			}
			else
			{
				// add this element as the input within a container
				$inputContainer = $fieldContainer->appendChild($this->createElement('div'));
				$inputContainer->setAttribute('class', 'input');
				$inputContainer->appendChild($input);
			}
		}
		else
		{
			// add the input as text to the input element
			$inputContainer = $fieldContainer->appendChild($this->createElement('div'));
			$inputContainer->setAttribute('class', 'input');
			$inputContainer->appendChild($this->createTextNode($input));
		}
		
		// add the description
		if ($description !== null)
		{
			$descriptionContainer = $fieldContainer->appendChild($this->createElement('div'));
			$descriptionContainer->setAttribute('class', 'description');
			
			// determine if the description provided is a DOM element
			if ($description instanceof DOMElement)
			{
				// add this element as the description for this form item
				$descriptionContainer->appendChild($description);
			}
			else
			{
				// add the description as text to the description element
				$descriptionContainer->appendChild($this->createTextNode($description));
			}
		}
		
		// return this element
		return $fieldContainer;
	}
	public function createTextInput($name, $value = '', $id = null, $size = 20, $maxLength = 0, $tabIndex = 0)
	{
		// create an input element
		$input = $this->createElement('input');
		
		// set the appropriate attributes
		$input->setAttribute('type', 'text');
		$input->setAttribute('name', $name);
		$input->setAttribute('value', $value);
		$input->setAttribute('size', $size);
		if ($id !== null)
		{
			$input->setAttribute('id', $id);
			
			// register this element with the id index
			$this->registerElementWithIdIndex($input);
		}
		
		// determine if there are additional attributes
		if ($maxLength > 0)
			$input->setAttribute('maxlength', $maxLength);
		if ($tabIndex > 0)
			$input->setAttribute('tabindex', $tabIndex);
		
		// return this element
		return $input;
	}
	public function createPasswordInput($name, $value = '', $id = null, $size = 20, $maxLength = 0, $tabIndex = 0)
	{
		// create a text input
		$input = $this->createTextInput($name, $value, $id, $size, $maxLength, $tabIndex);
		
		// change the type to password
		$input->setAttribute('type', 'password');
		
		// return this element
		return $input;
	}
	public function createHiddenInput($name, $value = '', $id = null)
	{
		// create an input element
		$input = $this->createElement('input');
		
		// set the appropriate attributes
		$input->setAttribute('type', 'hidden');
		$input->setAttribute('name', $name);
		$input->setAttribute('value', $value);
		if ($id !== null)
		{
			$input->setAttribute('id', $id);
			
			// register this element with the id index
			$this->registerElementWithIdIndex($input);
		}
		
		// return this element
		return $input;
	}
	public function createButtonInput($name, $value = '', $id = null, $tabIndex = 0)
	{
		// create a text input
		$input = $this->createTextInput($name, $value, $id, null, null, $tabIndex);
		
		// change the type to button
		$input->setAttribute('type', 'button');
		
		// return this element
		return $input;
	}
	public function createSubmitButtonInput($name, $value = '', $id = null, $tabIndex = 0)
	{
		// create a text input
		$input = $this->createTextInput($name, $value, $id, null, null, $tabIndex);
		
		// change the type to submit
		$input->setAttribute('type', 'submit');
		
		// return this element
		return $input;
	}
	public function createResetButtonInput($name, $value = '', $id = null, $tabIndex = 0)
	{
		// create a text input
		$input = $this->createTextInput($name, $value, $id, null, null, $tabIndex);
		
		// change the type to reset
		$input->setAttribute('type', 'reset');
		
		// return this element
		return $input;
	}
	public function createTextArea($name, $value = '',  $id = null, $columns = 40, $rows = 5, $tabIndex)
	{
		// create a textarea element
		$textarea = $this->createElement('textarea');
		
		// set the appropriate attributes
		$textarea->setAttribute('name', $name);
		$textarea->setAttribute('rows', $rows);
		$textarea->setAttribute('cols', $columns);
		$textarea->appendChild($this->createTextNode($value));
		if ($id !== null)
		{
			$input->setAttribute('id', $id);
			
			// register this element with the id index
			$this->registerElementWithIdIndex($input);
		}
		
		// determine if there are additional attributes
		if ($tabIndex > 0)
			$input->setAttribute('tabindex', $tabIndex);
		
		// return this element
		return $textarea;
	}
	public function createRadioInput($name, $value = '', $id = null, $checked = false, $tabIndex = 0)
	{
		// create an input element
		$input = $this->createElement('input');
		
		// set the appropriate attributes
		$input->setAttribute('type', 'radio');
		$input->setAttribute('name', $name);
		$input->setAttribute('value', $value);
		if ($id !== null)
		{
			$input->setAttribute('id', $id);
			
			// register this element with the id index
			$this->registerElementWithIdIndex($input);
		}
		if ($checked)
			$input->setAttribute('checked', 'yes');
		
		// determine if there are additional attributes
		if ($tabIndex > 0)
			$input->setAttribute('tabindex', $tabIndex);
		
		// return this element
		return $input;
	}
	public function createCheckboxInput($name, $value, $id = null, $checked = false, $tabIndex = 0)
	{
		// create an input element
		$input = $this->createRadioInput($name, $value, $id, $checked, $tabIndex);
		
		// set the appropriate attributes
		$input->setAttribute('type', 'checkbox');
		
		// return this element
		return $input;				
	}
	public function createLabeledCheckBoxInput($name, $value, $label, $id = null, $checked = false, $tabIndex = 0)
	{
/*
	Labeled check box inputs are check boxes coupled with a label that has some javascript in it to trigger the click action of the checkbox when the text of the label is clicked.
*/
		// create the label
		$labelElement = $this->createElement('label');
		
		// add the checkbox
		$labelElement->appendChild($this->createCheckBoxInput($name, $value, $id, $checked, $tabIndex));
		
		// add the text to the label
		$labelElement->appendChild($this->createTextNode(strval($label)));
		
		// return this element
		return $labelElement;
	}
	public function createSelect($name, $value = '', $id = null, $options = null, $label = '', $tabIndex = 0)
	{
/*
	Select menu options should be provided in a one dimensional associative array where the index is the intended option value and the value at that index is the intended option label. Please note that this does require that each option in the select have a unique value.
	The default option should be provided as the options.
*/
		// create a select element
		$select = $this->createElement('select');
		$select->setAttribute('name', $name);
		if ($id !== null)
		{
			$select->setAttribute('id', $id);
			
			// register this element with the id index
			$this->registerElementWithIdIndex($select);
		}
		
		// set up the selected value
		// determine if a selected value was provided
		if ($value != '')
		{
			// add an option for this label
			$select->appendChild
			(
				$this->createSelectOption
				(
					$value,
					isset($options[$value])?
						$options[$value]:
						''
				)
			);
			
			// add a spacer between the label and the rest
			$select->appendChild($this->createSelectOption('', '', true));
		}
		else if ($label != '')
		{
			// add an option for this label
			$select->appendChild($this->createSelectOption('', $label));
			
			// add a spacer between the label and the rest
			$select->appendChild($this->createSelectOption('', '', true));
		}
		
		// create the options
		foreach ($options as $optionValue => $optionLabel)
		{
			// add the provided label
			$select->appendChild($this->createSelectOption($optionValue, $optionLabel));
		}
		
		// determine if there are additional attributes
		if ($tabIndex > 0)
			$select->setAttribute('tabindex', $tabIndex);
		
		// return this element
		return $select;
	}
	public function createSelectOption($value = '', $label = '', $disabled = false)
	{
		// create an option element
		$option = $this->createElement('option');
		
		// add the value
		$option->setAttribute('value', $value);
		
		// determine if we have a label for this value
		if ($label != '')
		{
			// add the provided label
			$option->appendChild($this->createTextNode($label));
		}
		else
		{
			// add the value as the label
			$option->appendChild($this->createTextNode($value));
		}
		
		// determine if this option should be disabled
		if ($disabled === true)
		{
			$option->setAttribute('disabled', 'true');
		}
		
		// return this element
		return $option;
	}
	public function addScript($source = null, $content = null, $type = 'text/javascript', $language = 'javascript', $characterSet = 'utf-8')
	{
		// determine if a script source was provided
		$newScript = true;
		if ($source !== null)
		{
			// look in existing scripts to see if there is a match
			foreach ($this->scripts as $script)
			{
				if ($script->getAttribute('src') == $source)
					$newScript = false;
			}
		}
		if ($newScript)
		{
			// create a script tag
			$script = $this->headNode->appendChild($this->createElement('script'));
			if ($source !== null)
			{
				$script->setAttribute('src', $source);
			}
			if ($content !== null)
			{
				$script->appendChild($this->createTextNode($content));
			}
			$script->setAttribute('type', $type);
			$script->setAttribute('language', $language);
			if ($characterSet != '')
			{
				$script->setAttribute('charset', $characterSet);
			}
		}
		return $this;
	}
	public function addStylesheet($source, $media = 'all')
	{
		// look in the existing links to see if there is a match
		$newStylesheet = true;
		foreach ($this->stylesheets as $link)
		{
			if ($link->getAttribute('href') == $source)
				$newStylesheet = false;
		}
		if ($newStylesheet)
		{
			// create a link tag
			$link = $this->headNode->appendChild($this->createElement('link'));
			$link->setAttribute('href', $source);
			$link->setAttribute('rel', 'stylesheet');
			$link->setAttribute('media', $media);
			$this->stylesheets[] = $link;
		}
		return $this;
	}
	public function debugDumpVariable($var, $label = '')
	{
		// create a new element to contain the variable
		$div = $this->createElement('div');
		
		// add a label
		if ($label != null)
		{
			$div->appendChild($this->createElement('h1', $label));
		}
		
		// recurse through the variable contents
		$this->debugRecurseDumpVariable($var, $div);
		
		// return the finished dom tree
		return $div;
	}
	public function debugRecurseDumpVariable($var, DomElement $container)
	{
		// process this variable
		if (is_array($var) || is_object($var))
		{
			// add a definition list for this variable
			$dl = $container->appendChild($this->createElement('dl'));
			
			// process each item
			foreach ($var as $key => $value)
			{
				// add a new term for this key
				$dl->appendChild($this->createElement('dt', $key));
				
				// create a definition for the value
				$dd = $dl->appendChild($this->createElement('dd'));
				
				// add this value
				$this->debugRecurseDumpVariable($value, $dd);
			}
		}
		else
		{
			// this should be a discrete value
			$container->appendChild($this->createTextNode($var));
		}
	}
	public function __toString()
	{
		return $this->cleanup()->saveXML();
	}
	public function saveXML()
	{
		// clean up references
		// javascript
		// css
		
		return parent::saveXML();
	}
	public function saveHTML()
	{
		return $this->saveXML();
	}
	public function save()
	{
		// clean up references
		// javascript
		// css
		
		return parent::saveXML();
	}
	public function saveHTMLFile()
	{
		return $this->save();
	}
}
?>