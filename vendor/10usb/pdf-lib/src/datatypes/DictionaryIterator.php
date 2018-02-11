<?php
namespace pdflib\datatypes;

/**
 * 
 * @author 10usb
 */
class DictionaryIterator implements \Iterator {
	/**
	 * 
	 * @var array
	 */
	private $entries;
	
	/**
	 * 
	 * @var number
	 */
	private $index;
	
	/**
	 * 
	 * @param array $entries
	 */
	public function __construct($entries){
		$this->entries	= $entries;
		$this->index	= 0;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see Iterator::rewind()
	 */
	public function rewind(){
		$this->index = 0;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @return \pdflib\datatypes\Object
	 * @see Iterator::current()
	 */
	public function current(){
		return $this->entries[$this->index]->value;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @return \pdflib\datatypes\Name
	 * @see Iterator::key()
	 */
	public function key(){
		return $this->entries[$this->index]->key;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see Iterator::next()
	 */
	public function next(){
		++$this->index;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see Iterator::valid()
	 */
	public function valid(){
		return isset($this->entries[$this->index]);
	}
}