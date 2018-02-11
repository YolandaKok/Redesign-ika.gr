<?php
namespace pdflib\datatypes;

class Boolean implements Object {
	const FALSE = new self(false);
	const TRUE = new self(true);
	
	private $value;
	
	public function __construct($value){
		$this->value = !!$value;
	}
	
	public function output(){
		return $this->value ? 'true' : 'false';
	}
}