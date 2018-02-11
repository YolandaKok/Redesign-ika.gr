<?php
namespace pdflib\datatypes;

class Number implements Object {
	private $value;
	
	public function __construct($value){
		if(!is_numeric($value)) throw new \Exception('Value "'.$value.'" is not a number');
		$this->value = $value;
		
	}
	
	public function output(){
		return $this->value;
	}
	
	public function getValue(){
		return $this->value;
	}
}