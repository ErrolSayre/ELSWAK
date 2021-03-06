<?php
/*
	ELSWAK Store Search
	
	Store search "queries" are designed to provide a common interface for providing search data to a store coordinator. Since a store coordinator can be any type (RDBMS, XML, text, or binary file, etc.) controllers need a standard and independent manner to provide search parameters. A store search is made up of a search criteria list, a sort properties list, a depth, and a limit.
*/
class ELSWAK_Store_Search
	extends ELSWAK_Settable {
	protected $criteriaList;
	protected $sortItems;
	protected $depth;
	protected $limit;
	
	public function __construct(array $criteriaList = null, array $sortItems = null, $depth = 'deep', ELSWAK_Store_Search_Limit $limit = null) {
		$this->setCriteriaList($criteriaList ?: array());
		$this->setSortItems($sortItems?: array());
		$this->setDepth($depth);
		$this->setLimit($limit?: new ELSWAK_Store_Search_Limit);
	}
	public function criteriaForKey($index) {
		if (isset($this->criteriaList[$index])) {
			return $this->criteriaList[$index];
		} else {
			throw new Exception('Invalid key: Criteria not found.');
		}
	}
	public function addCriteria(ELSWAK_Store_Search_Criteria $criteria) {
		$this->criteriaList[] = $criteria;
		return $this->criteriaList[count($this->criteriaList) - 1];
	}
	public function removeCriteriaForKey($index) {
		if (isset($this->criteriaList[$index])) {
			array_splice($this->criteriaList, $index, 1);
		} else {
			throw new Exception('Invalid key: Criteria not removed');
		}
	}
	public function criteriaCount() {
		return count($this->criteriaList);
	}
	public function hasCriteria() {
		if ($this->criteriaCount() > 0)
			return true;
		return false;
	}
	public function criteriaKeys() {
		return array_keys($this->criteriaList);
	}
	public function criteriaList() {
		return $this->criteriaList;
	}
	public function setCriteriaList(array $criteriaList) {
		$this->criteriaList = array();
		
		foreach ($criteriaList as $criteria) {
			if ($criteria instanceOf ELSWAK_Store_Search_Criteria) {
				$this->addCriteria($criteria);
			}
		}
		return $this;
	}
	public function sortItemForKey($index) {
		if (isset($this->sortItems[$index])) {
			return $this->sortItems[$index];
		} else {
			throw new Exception('Invalid key: Criteria not found.');
		}
	}
	public function addSortItem(ELSWAK_Store_Search_Sort $sortItem) {
		$this->sortItems[] = $sortItem;
		return $this->sortItems[count($this->sortItems) - 1];
	}
	public function removeSortItemForKey($index) {
		if (isset($this->sortItems[$index])) {
			array_splice($this->sortItems, $index, 1);
		} else {
			throw new Exception('Invalid key: Criteria not removed');
		}
	}
	public function sortItemCount() {
		return count($this->sortItems);
	}
	public function hasSortItems() {
		if ($this->sortItemCount() > 0)
			return true;
		return false;
	}
	public function sortItemKeys() {
		return array_keys($this->sortItems);
	}
	public function sortItems() {
		return $this->sortItems;
	}
	public function setSortItems(array $sortItems) {
		$this->sortItems = array();
		
		foreach ($sortItems as $sortItem) {
			if ($sortItem instanceOf ELSWAK_Store_Search_Sort) {
				$this->addSortItem($sortItem);
			}
		}
		return $this;
	}
	public function depth() {
		return $this->depth;
	}
	public function setDepth($depth) {
		$depth = strtolower($depth);
		
		if ($depth == 'shallow') {
			$this->depth = 'shallow';
		} else if ($depth == 'deep') {
			$this->depth = 'deep';
		} else if ($depth == 'complete') {
			$this->depth = 'complete';
		} else {
			throw new Exception('Invalid depth: depth not set.');
		}
		return $this;
	}
	public function limit() {
		return $this->limit;
	}
	public function setLimit(ELSWAK_Store_Search_Limit $limit) {
		$this->limit = $limit;
		return $this;
	}
}
?>