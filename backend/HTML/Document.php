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
		return $this->headNode;
	}
	public function body()
	{
		return $this->bodyNode;
	}
	public function addContent($content)
	{
		if ($content instanceof DOMNode)
			return $this->bodyNode->appendChild($content);
		else
			return $this->bodyNode->appendChild($this->createElement('div', $content));
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
					$this->elementIdIndex[$otherId] = $this->elementIdIndex[$id];
				
				// remove the current reference for the given id
				$this->elementIdIndex[$id] = null;
				
				// look for the element within the document's native mechanism
				if ($this->getElementById($id) !== null)
					return $this->searchDomTreeForElementById($this->getElementById($id), $id);
				
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
				return $node;
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
					return $found;
				
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
			$this->elementIdIndex[$node->getAttribute('id')] = $node;
		else
			throw new Exception('Element not registered: node must be a valid element with an id attribute.');
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
				$this->headNode->removeChild($titleElement);
			
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
	public function createElement($tagName, $content = null, array $attributes = null)
	{
		$element = parent::createElement($tagName);
		
		// determine if any content was provided
		if ($content instanceof DOMElement)
			$element->appendChild($content);
		else if ($content !== null)
			$element->appendChild($this->createTextNode($content));
		
		// set attributes
		if (is_array($attributes))
		{
			foreach ($attributes as $attributeKey => $attributeValue)
				$element->setAttribute($attributeKey, $attributeValue);
			if (in_array('id', array_keys($attributes)))
				$this->registerElementWithIdIndex($element);
		}
		return $element;
	}
	public function createLink($href, $content = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['href']))
			$attributes['href'] = $href;
		return $this->createElement('a', $content, $attributes);
	}
	public function createForm($action, $method = 'POST', $content = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['action']))
			$attributes['action'] = $action;
		if (empty($attributes['method']))
			$attributes['method'] = $method;
		return $this->createElement('form', $content, $attributes);
	}
	public function createFieldset($legend = null, $content = null, array $attributes = null)
	{
		// create a new fieldset element
		$fieldset = $this->createElement('fieldset', $content, $attributes);
		if (!empty($legend))
			$fieldset->insertBefore($this->createElement('legend', $legend), $fieldset->firstChild);
		return $fieldset;
	}
	public function createFormField($label, $input, $description = null)
	{
/*
	A "form field" in this document is made of a "field" container, which has a "label", "input" and "description".
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
	public function createTextArea($name, $value = null,  array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['columns']))
			$attributes['columns'] = 40;
		if (empty($attributes['rows']))
			$attributes['rows'] = 5;
		return $this->createElement('textarea', $value, $attributes);
	}
	public function createHiddenInput($name, $value = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'hidden';
		if (empty($attributes['name']))
			$attributes['name'] = $name;
		if (empty($attributes['value']))
			$attributes['value'] = $value;
		return $this->createElement('input', null, $attributes);
	}
	public function createTextInput($name, $value = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'text';
		if (empty($attributes['size']))
			$attributes['size'] = 20;
		return $this->createHiddenInput($name, $value, $attributes);
	}
	public function createPasswordInput($name, $value = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'password';
		return $this->createTextInput($name, $value, $attributes);
	}
	public function createButtonInput($name, $value = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'button';
		return $this->createHiddenInput($name, $value, $attributes);
	}
	public function createSubmitButtonInput($name, $value = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'submit';
		return $this->createHiddenInput($name, $value, $attributes);
	}
	public function createResetButtonInput($name, $value = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'reset';
		return $this->createHiddenInput($name, $value, $attributes);
	}
	public function createRadioInput($name, $value = null, $checked = false, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'radio';
		if (empty($attributes['checked']) && $checked)
			$attributes['checked'] = 'yes';
		return $this->createHiddenInput($name, $value, $attributes);
	}
	public function createCheckboxInput($name, $value, $checked = false, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = 'checkbox';
		if (empty($attributes['checked']) && $checked)
			$attributes['checked'] = 'yes';
		return $this->createHiddenInput($name, $value, $attributes);
	}
	public function createLabeledCheckBoxInput($name, $value, $label, $checked = false, array $attributes = null)
	{
/*
	Labeled check box inputs are check boxes coupled with a label so that the checkbox is toggled when the text of the label is clicked.
*/
		$labelElement = $this->createElement('label');
		$labelElement->appendChild($this->createCheckBoxInput($name, $value, $checked, $attributes));
		$labelElement->appendChild($this->createTextNode(strval($label)));
		return $labelElement;
	}
	public function createSelect($name, $selectedValue = null, array $options = null, $noValueLabel = null, array $attributes = null)
	{
/*
	Select menu options should be provided in a multi-dimensional array where the intended option value and content are provided in an associative array e.g. array('value'=>'asdf','content'=>'ASDF').
	A selected value can be provided as an option array or as a scalar value. If a scalar is provided, the given options will be searched, or the value will be used as it's own label.
	If no selected value is provided, a "label" can be provided that can serve as the first option e.g. "Please select an option".
*/
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['name']))
			$attributes['name'] = $name;
		$select = $this->createElement('select', null, $attributes);
		
		// set up the first value
		if (is_array($selectedValue))
		{
			$optionValue = isset($selectedValue['value'])? $selectedValue['value']: '';
			$optionContent = isset($selectedValue['content'])? $selectedValue['content']: $noValueLabel;
			$select->appendChild($this->createSelectOption($optionValue, $optionContent));
		}
		else if (!empty($selectedValue) && (is_array($options) && !empty($options[$selectedValue])))
			$select->appendChild($this->createSelectOption($selectedValue, $options[$selectedValue]));
		else if (!empty($noValueLabel))
			$select->appendChild($this->createSelectOption(null, $noValueLabel));
		
		// add a spacer between the label and the rest
		$select->appendChild($this->createSelectOption(null, null, array('disabled' => true)));
		
		// create the options
		if (is_array($options))
			foreach ($options as $option)
			{
				$optionValue = !empty($option['value'])? $option['value']: '';
				$optionContent = !empty($option['content'])? $option['content']: '';
				$attributes = array();
				if (is_array($selectedValue) && !empty($selectedValue['value']) && ($optionValue == $selectedValue['value']))
					$attributes['selected'] = 'true';
				else if (!empty($selectedValue) && ($optionValue == $selectedValue))
					$attributes['selected'] = 'true';
				$select->appendChild($this->createSelectOption($optionValue, $optionContent, $attributes));
			}
		return $select;
	}
	public function createSelectOption($value = null, $content = null, array $attributes = null)
	{
		$option = $this->createElement('option', null, $attributes);
		
		// determine how to label this option
		if (!empty($value) && !empty($content))
		{
			$option->appendChild($this->createTextNode($content));
			$option->setAttribute('value', $value);
		}
		else if (!empty($content))
			$option->appendChild($this->createTextNode($content));
		else if (!empty($value))
			$option->appendChild($this->createTextNode($value));

		return $option;
	}
	public function addScript($source = null, $content = null, $useHeader = false, array $attributes = null)
	{
		// determine if a script source was provided and prevent duplicates
		$uniqueScript = true;
		if ($source !== null)
			foreach ($this->scripts as $script)
				if ($script->getAttribute('src') == $source)
					$uniqueScript = false;
		
		if ($uniqueScript)
		{
			if (!is_array($attributes))
				$attributes = array();
			if (empty($attributes['src']))
				$attributes['src'] = $source;
			if (empty($attributes['type']))
				$attributes['type'] = 'text/javascript';
			if (empty($attributes['language']))
				$attributes['language'] = 'javascript';
			if (empty($attributes['charset']))
				$attributes['charset'] = 'utf-8';
			
			// defaultly add scripts to the end of the document, unless requested to add it to the header
			$targetNode = $this->bodyNode;
			if ($useHeader)
				$targetNode = $this->headNode;
			$this->scripts[] = $targetNode->appendChild($this->createElement('script', $content, $attributes));
		}
		return $this;
	}
	public function addStyle($content, $type = 'text/css', $media = 'all', array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['type']))
			$attributes['type'] = $type;
		if (empty($attributes['media']))
			$attributes['media'] = $media;
		$this->headNode->appendChild($this->createElement('style', $content, $attributes));
		return $this;
	}
	public function addStylesheet($source, $media = 'all', array $attributes = null)
	{
		// prevent duplicate stylesheets from being added
		$uniqueStylesheet = true;
		foreach ($this->stylesheets as $link)
			if ($link->getAttribute('href') == $source)
				$uniqueStylesheet = false;
		
		if ($uniqueStylesheet)
		{
			if (!is_array($attributes))
				$attributes = array();
			if (empty($attributes['href']))
				$attributes['href'] = $source;
			if (empty($attributes['rel']))
				$attributes['rel'] = 'stylesheet';
			if (empty($attributes['media']))
				$attributes['media'] = $media;
			$this->stylesheets[] = $this->headNode->appendChild($this->createElement('link', null, $attributes));
		}
		return $this;
	}
	public function debugDumpVariable($var, $label = '')
	{
		// create a new element to contain the variable
		$div = $this->createElement('div');
		if ($label != null)
			$div->appendChild($this->createElement('h1', $label));
		// recurse through the variable contents
		$this->debugRecurseDumpVariable($var, $div);
		return $div;
	}
	public function debugRecurseDumpVariable($var, DOMElement $container)
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
		return parent::saveXML();
	}
	public function saveHTML()
	{
		return $this->saveXML();
	}
	public function save()
	{
		return parent::saveXML();
	}
	public function saveHTMLFile()
	{
		return $this->save();
	}
}
