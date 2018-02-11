<?php
namespace pdflib\datatypes;

class Collection implements Object, \IteratorAggregate {
	/**
	 * 
	 * @var \pdflib\datatypes\Object[]
	 */
	private $values;
	
	/**
	 * 
	 * @param \pdflib\datatypes\Object[] $values
	 */
	public function __construct($values = []){
		$this->values = [];
		foreach($values as $value) $this->push($value);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \pdflib\datatypes\Object::output()
	 */
	public function output(){
		$outputs = [];
		foreach($this->values as $value){
			$outputs[] = $value->output();
		}
		return '['.implode(' ', $outputs).']';
	}
	
	/**
	 * 
	 * @param integer $index
	 * @return \pdflib\datatypes\Object
	 */
	public function get($index){
		if(!isset($this->values[$index])) throw new \Exception('Index out of bound');
		return $this->values[$index];
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Object|array $value
	 * @return \pdflib\datatypes\Collection
	 */
	public function push($value){
		if($value instanceof Object){
			$this->values[] = $value;
		}elseif(is_array($value)){
			$this->values[] = new self($value);
		}else{
			throw new \Exception('Not an object type');
		}
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \IteratorAggregate::getIterator()
	 */
	public function getIterator(){
		return new \IteratorIterator(new \ArrayIterator($this->values));
	}
}