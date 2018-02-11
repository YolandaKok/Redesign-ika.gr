<?php
namespace pdflib\datatypes;

/**
 * 
 * @author 10usb
 */
class Dictionary implements Object, \IteratorAggregate {
	/**
	 * 
	 * @var array
	 */
	private $entries;
	
	/**
	 * 
	 */
	public function __construct(){
		$this->entries = [];
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \pdflib\datatypes\Object::output()
	 */
	public function output(){
		$outputs = [];
		foreach($this->entries as $value){
			$outputs[] = $value->key->output().' '.$value->value->output();
		}
		return '<<'.implode(' ', $outputs).'>>';
	}
	
	/**
	 * {@inheritDoc}
	 * @see \IteratorAggregate::getIterator()
	 */
	public function getIterator(){
		return new DictionaryIterator($this->entries);
	}
	
	/**
	 * 
	 * @param string $key
	 * @return \pdflib\datatypes\Object|boolean
	 */
	public function get($key){
		foreach($this->entries as $entry){
			if($entry->key == $key){
				return $entry->value;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param string|\pdflib\datatypes\Name $key
	 * @param \pdflib\datatypes\Object $value
	 * @return \pdflib\datatypes\Dictionary
	 */
	public function set($key, $value){
		if(!$value instanceof Object){
			throw new \Exception('Not an object type');
		}
		if(!$key instanceof Name){
			$key = new Name($key);
		}
		
		foreach($this->entries as $entry){
			if($entry->key == $key){
				$entry->value = $value;
				return $this;
			}
		}
		
		$entry = new \stdClass();
		$entry->key		= $key;
		$entry->value	= $value;
		$this->entries[] = $entry;
		return $this;
	}
	
	/**
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key){
		foreach($this->entries as $index=>$entry){
			if($entry->key == $key){
				unset($this->entries[$idex]);
				$this->entries = array_values($this->entries);
				return true;
			}
		}
		return false;
	}
}