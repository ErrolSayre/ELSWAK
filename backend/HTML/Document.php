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
			$this->load($templateFile);
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
		
		// setup the primary content container
		$this->contentNode = $this->bodyNode;
		
		// collect any scripts in the template
		$this->scripts = array();
		foreach ($this->getElementsByTagName('script') as $script)
			$this->scripts[] = $script;
		
		// collect any stylesheets in the template
		$this->stylesheets = array();
		foreach ($this->getElementsByTagName('link') as $link)
			if (strtolower($link->getAttribute('rel')) == 'stylesheet')
				$this->stylesheets[] = $link;
		
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
	public function messages($delimiter = null)
	{
		// since the messages are stored directly into the document, none are kept separate
		return false;
	}
	public function addMessage($message, $key = null, $type = null)
	{
		return $this->addContent($message, $key, $type);
	}
	public function setContent($content = null, $key = null, $type = null)
	{
		// like the original function in the HTTP response, overwrite the existing content within the document with that provided
		$this->removeChildren($this->contentNode);
		return $this->addContent($content, $key, $type);
	}
	public function addContent($content, $key = null, $type = null)
	{
		// append content to the body or set/overwrite the value of a given key if provided
		if ($key !== null)
			return $this->setContentForKey($content, $key, $type);
		else
			$this->contentNode->appendChild($this->importContent($content, $key, $type));
		return $this;
	}
	public function setContentForKey($content, $key, $type = null)
	{
		// overwrite content matching the given key
		$element = $this->locateElementById($key);
		if ($element instanceof DOMElement)
			// replace this element with the provided content
			$this->contentNode->replaceChild($this->importContent($content, $key, $type), $element);
		else
			$this->contentNode->appendChild($this->importContent($content, $key, $type));
		return $this;
	}
	protected function importContent($content, $key, $type)
	{
		if ($content instanceof DOMNode)
		{
			// determine if the node is an element
			if ($content instanceof DOMElement)
				if (!empty($key))
				{
					$content->setAttribute('id', $key);
					$this->registerElementWithIdIndex($content);
				}
			return $content;
		}
		else if (is_string($content) && strtolower($type) == 'html')
			return $this->convertHTML($content, $key);
		return $this->convertVariable($content, $key);
	}
	public function convertHTML($html, $key = null)
	{
		// convert the supplied html/xml string into a dom tree and import to the local document
		// wrap the provided html in proper tags and create a new dom
		$document = new DOMDocument();
		$document->loadHTML('<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>'.$html.'</body></html>');
		
		// grab the body of the new document
		$body = $document->getElementsByTagName('body')->item(0);
		
		// determine how many children the body has
		if ((count($body->childNodes) == 1) && ($body->firstChild instanceof DOMElement))
		{
			$element = $this->importNode($body->firstChild, true);
			$element->setAttribute('id', $key);
			$this->registerElementWithIdIndex($element);
			return $element;
		}
		
		// create a container for the new content
		$container = $this->createDiv(null, array('id' => $key));
		$this->registerElementWithIdIndex($container);
		
		// import all the children of the new document's body into this container
		while ($body->hasChildNodes())
		{
			$container->appendChild($this->importNode($body->firstChild, true));
			$body->removeChild($body->firstChild);
		}
		return $container;
	}
	public function convertVariable($content, $key = null)
	{
		// convert the supplied variable into a dom tree on the local document
		if (is_array($content) ||
			(is_object($content) && !method_exists($content, '__toString')))
			return $this->debugDumpVariable($content, $key);
		
		// try to import the contents of the variable into a div
		return $this->createDiv($content, array('id' => $key));
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
	public function registerElementWithIdIndex(DOMElement $node)
	{
		// determine if the current node has an id
		if ($node->hasAttribute('id'))
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
			{
				// format boolean values properly
				if (is_bool($attributeValue))
					$element->setAttribute($attributeKey, ($attributeValue)? 'true': 'false');
				else
					$element->setAttribute($attributeKey, $attributeValue);
			}
			if (array_key_exists('id', $attributes) == true)
			{
				$element->setAttribute('id', $attributes['id']);
				$this->registerElementWithIdIndex($element);
			}
		}
		return $element;
	}
	public function addClassToElement($class, DOMElement $element)
	{
		// setup the existing classes
		$classes = array();
		if ($element->hasAttribute('class'))
			$classes = explode(' ', $element->getAttribute('class'));
		
		// determine if this class is new
		if (!in_array($class, $classes))
			$classes[] = $class;
		
		// set the class
		$element->setAttribute('class', implode(' ', $classes));
	}
	public function createDiv($content = null, array $attributes = null)
	{
		return $this->createElement('div', $content, $attributes);
	}
	public function createLink($href, $content = null, array $attributes = null)
	{
		if (!is_array($attributes))
			$attributes = array();
		if (empty($attributes['href']))
			$attributes['href'] = $href;
		return $this->createElement('a', $content, $attributes);
	}
	public function createForm($action = '', $method = 'POST', $content = null, array $attributes = null)
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
	public function createFormField($label, $input, $description = null, array $attributes = null)
	{
/*
	A "form field" in this document is made of a "field" container, which has a "label", "input" and "description".
*/
		// set up the class
		if ($attributes == null)
			$attributes = array();
		if (empty($attributes['class']))
			$attributes['class'] = '';
		$attributes['class'] = 'field '.$attributes['class'];
		
		// create the field container
		$fieldContainer = $this->createElement('div', null, $attributes);
		$this->addClassToElement('field', $fieldContainer);
		
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
			if ((strtolower($input->tagName) == 'input') ||
				($input->tagName == 'div' && $input->hasAttribute('class') && $input->getAttribute('class') == 'input'))
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
		if (empty($attributes['name']))
			$attributes['name'] = $name;
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
	public function createLabeledRadioInput($name, $value, $label, $checked = false, array $attributes = null)
	{
/*
	Labeled check box inputs are check boxes coupled with a label so that the radio is toggled when the text of the label is clicked.
*/
		$labelElement = $this->createElement('label');
		$labelElement->appendChild($this->createRadioInput($name, $value, $checked, $attributes));
		if ($label instanceof DOMElement)
			$labelElement->appendChild($label);
		else
			$labelElement->appendChild($this->createTextNode(strval($label)));
		return $labelElement;
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
	public function createLabeledCheckboxInput($name, $value, $label, $checked = false, array $attributes = null)
	{
/*
	Labeled check box inputs are check boxes coupled with a label so that the checkbox is toggled when the text of the label is clicked.
*/
		$labelElement = $this->createElement('label');
		$labelElement->appendChild($this->createCheckboxInput($name, $value, $checked, $attributes));
		if ($label instanceof DOMElement)
			$labelElement->appendChild($label);
		else
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
			if (empty($attributes['src']) && !empty($source))
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
			$script = $targetNode->appendChild($this->createElement('script', null, $attributes));
			if ($content !== null)
				$script->appendChild($this->createTextNode($content));
			$this->scripts[] = $script;
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
	public function removeChildren(DOMNode $node)
	{
		while ($node->hasChildNodes())
			$node->removeChild($node->firstChild);
	}
	public function __toString()
	{
		return $this->cleanup()->save();
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
